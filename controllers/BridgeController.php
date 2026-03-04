<?php

namespace app\controllers;

use app\helpers\DBHelper;
use app\helpers\DWHelper;
use app\helpers\MyHelper;
use app\models\Abstraction;
use app\models\AbstractionColumn;
use app\models\Bridge;
use app\models\BridgeColumn;
use app\models\BridgeSearch;
use app\models\Entity;
use app\models\EntityAffiliation;
use app\models\EntitySystem;
use app\models\System;
use Exception;
use mysqli;
use PhpParser\Node\NullableType;
use Yii;
use yii\db\mssql\PDO;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * BridgeController implements the CRUD actions for Bridge model.
 */
class BridgeController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Bridge models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BridgeSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Bridge model.
     * @param string $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id = null)
    {
        $bridgeColumn = BridgeColumn::find()->where(['bridge_id' => $id])->all();

        $bridgeColumnList = ArrayHelper::map($bridgeColumn, 'target_column_name', 'source_column_name');

        if ($id == null) {
            $system_code = Yii::$app->request->get('system_code');
            $bridge_table_source = Yii::$app->request->get('bridge_table_source');

            $model = Bridge::find()->where([
                'system_code' => $system_code,
                'bridge_table_source' => $bridge_table_source
            ])->one();

            if ($model == null) {
                $model = new Bridge();
                $model->id = MyHelper::genuuid();
                $model->system_code = $system_code;
                $model->bridge_table_source = $bridge_table_source;
                $model->save();
            } else {
                $id = $model->id ?? null;
            }
        } else {
            $model = $this->findModel($id);
        }

        return $this->render('view', [
            'model' => $model,
            'bridgeColumnList' => $bridgeColumnList,
        ]);
    }

    /**
     * Creates a new Bridge model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Bridge();
        $model->status = 'active';
        $system = ArrayHelper::map(System::find()->orderBy('system_name')->all(), 'system_code', 'system_name');
        $DWInfo = DWHelper::getDWInfoFromCache();

        $dwTables = array_keys($DWInfo['result']['data']['tables']);
        $dwTables  = array_combine($dwTables, $dwTables);

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'uuid' => MyHelper::genuuid(),
            'system' => $system,
            'dwTables' => $dwTables,
        ]);
    }

    public function actionRun($id)
    {
        $model = $this->findModel($id);
        $database = System::findOne(['system_code' => $model->system_code]);

        $RAW_DATA = [];
        $execute_list = [];

        /* ==========================
       SOURCE MYSQL
    ========================== */

        $mysqli = new mysqli(
            $database->hostname,
            $database->username,
            $database->password,
            $database->database_name,
            $database->port
        );

        if ($mysqli->connect_error) {
            throw new Exception($mysqli->connect_error);
        }

        $tableName = $model->bridge_table_source;

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
            throw new Exception("Invalid table name");
        }

        $columnList = BridgeColumn::find()
            ->select('source_column_name')
            ->where(['bridge_id' => $id])
            ->column();

        $sql = "SELECT `" . implode('`, `', $columnList) . "` 
            FROM `$tableName`
            LIMIT 100";

        $result = $mysqli->query($sql);

        while ($row = $result->fetch_assoc()) {
            $RAW_DATA[] = $row;
        }

        $result->free();
        $mysqli->close();

        if (empty($RAW_DATA)) {
            Yii::$app->session->setFlash('info', 'No data found.');
            return $this->redirect(['view', 'id' => $id]);
        }

        /* ==========================
       FILTER EXISTING
    ========================== */

        $sourceIds = array_column($RAW_DATA, 'id');

        $existingReferences = EntitySystem::find()
            ->select('entity_reference')
            ->where([
                'system_code' => $model->system_code,
                'entity_reference' => $sourceIds
            ])
            ->column();

        $existingMap = array_flip($existingReferences);

        /* ==========================
       PREPARE BATCH DATA
    ========================== */

        $entityRows = [];
        $entitySystemRows = [];
        $entityAffiliationRows = [];

        foreach ($RAW_DATA as $data) {

            if (isset($existingMap[$data['id']])) {
                continue;
            }

            $execute_list[] = $data;

            $entityId = MyHelper::genEntityId();
            $uuid = MyHelper::genuuid();
            $now = date('Y-m-d H:i:s');

            $entityRows[] = [
                $uuid,
                $entityId,
                'active',
                'unknown'
            ];

            $entitySystemRows[] = [
                MyHelper::genuuid(),
                $entityId,
                $model->system_code,
                $data['id'],
                $now,
                $now
            ];

            $entityAffiliationRows[] = [
                MyHelper::genuuid(),
                $entityId,
                $data['id'],
                'IJN'
            ];
        }

        /* ==========================
       BATCH INSERT MYSQL
    ========================== */

        $transaction = Yii::$app->db->beginTransaction();

        try {

            if (!empty($entityRows)) {

                Yii::$app->db->createCommand()->batchInsert(
                    Entity::tableName(),
                    ['id', 'entity_id', 'status', 'is_alive'],
                    $entityRows
                )->execute();

                Yii::$app->db->createCommand()->batchInsert(
                    EntitySystem::tableName(),
                    ['id', 'entity_id', 'system_code', 'entity_reference', 'created_at_data', 'updated_at_data'],
                    $entitySystemRows
                )->execute();

                Yii::$app->db->createCommand()->batchInsert(
                    EntityAffiliation::tableName(),
                    ['id', 'entity_id', 'entity_reference', 'affiliation_code'],
                    $entityAffiliationRows
                )->execute();
            }

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        /* ==========================
       BULK INSERT POSTGRES
    ========================== */

        if (!empty($execute_list)) {

            $dsn = "pgsql:host=34.71.143.136;port=5432;dbname=datawarehouse";

            $pdo = new PDO($dsn, 'appuser', 'AppPass!123', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            $columns = array_keys($execute_list[0]);

            $values = [];
            $params = [];

            foreach ($execute_list as $i => $row) {

                $placeholders = [];

                foreach ($columns as $col) {
                    $param = ":{$col}_{$i}";
                    $placeholders[] = $param;
                    $params[$param] = $row[$col];
                }

                $values[] = "(" . implode(',', $placeholders) . ")";
            }

            $sql = "INSERT INTO {$model->bridge_table_target} 
                (" . implode(',', $columns) . ")
                VALUES " . implode(',', $values);

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        }

        Yii::$app->session->setFlash('success', 'Bridge execution completed.');
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Updates an existing Bridge model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $abstractionData = Abstraction::find()->all();
        $abstraction = ArrayHelper::map($abstractionData, 'id', function ($model) {
            return $model->table_warehouse;
        });


        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        // prepare initial tables for the selected system
        $initialTables = [];
        $selectedSystemCode = $model->system_code ?? '';
        if ($selectedSystemCode) {
            $sysModel = System::find()->where(['system_code' => $selectedSystemCode])->one();
            if ($sysModel) {
                try {
                    if (strpos(strtolower($sysModel->system_type ?? ''), 'mysql') !== false) {
                        $params = [
                            'system_code' => $sysModel->system_code,
                            'hostname' => $sysModel->hostname,
                            'username' => $sysModel->username,
                            'password' => $sysModel->password,
                            'port' => $sysModel->port,
                            'database' => $sysModel->database_name,
                            'use_cache' => false,
                        ];
                        $res = \app\helpers\DBHelper::testConMysql($params);
                        if (is_array($res) && ($res['status'] ?? '') === 'success') {
                            $initialTables = array_combine(array_values($res['data']['tables'] ?? []), array_values($res['data']['tables'] ?? []));
                        }
                    } else {
                        $host = $sysModel->hostname;
                        $port = $sysModel->port ?: 5432;
                        $dbname = $sysModel->database_name;
                        $user = $sysModel->username;
                        $pass = $sysModel->password;

                        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
                        $pdo = new \PDO($dsn, $user, $pass, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
                        $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
                        $tables = [];
                        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                            $tables[] = $row['table_name'];
                        }
                        if (!empty($tables)) {
                            $initialTables = array_combine($tables, $tables);
                        }
                    }
                } catch (\Exception $e) {
                    // ignore and let JS fetch on client
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'system' => ArrayHelper::map(System::find()->orderBy('system_name')->all(), 'system_code', 'system_name'),
            'bridgeType' => MyHelper::bridgeType(),
            'abstraction' => $abstraction,
            'initialTables' => $initialTables,
        ]);
    }

    /**
     * Deletes an existing Bridge model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Bridge model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Bridge the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bridge::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Returns available warehouse tables for a given system_code.
     * Used by dependent dropdowns via AJAX.
     * @param string|null $system_code
     * @return \yii\web\Response
     */
    public function actionGetTables($system_code = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (empty($system_code)) {
            return ['status' => 'error', 'message' => 'Missing system_code'];
        }

        $system = System::find()->where(['system_code' => $system_code])->one();
        if ($system === null) {
            return ['status' => 'error', 'message' => 'System not found'];
        }

        $systemType = strtolower($system->system_type ?? '');

        try {
            if (strpos($systemType, 'mysql') !== false) {

                $res = DBHelper::getDatabaseInfoFromCache($system);
                if (!is_array($res) || ($res['status'] ?? '') !== 'success') {
                    return ['status' => 'error', 'message' => 'Unable to fetch tables', 'detail' => $res];
                }

                $tables = array_keys($res['result']['tables'] ?? []);
            } else {
                // Assume PostgreSQL-like: fetch via PDO
                $host = $system->hostname;
                $port = $system->port ?: 5432;
                $dbname = $system->database_name;
                $user = $system->username;
                $pass = $system->password;

                $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
                $pdo = new \PDO($dsn, $user, $pass, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
                $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
                $tables = [];
                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                    $tables[] = $row['table_name'];
                }
            }

            return ['status' => 'success', 'tables' => $tables];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
