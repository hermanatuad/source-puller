<?php

namespace app\controllers;

use app\helpers\DBHelper;
use app\helpers\DWHelper;
use app\helpers\MyHelper;
use app\models\Abstraction;
use app\models\AbstractionColumn;
use app\models\Bridge;
use app\models\BridgeSearch;
use app\models\System;
use PhpParser\Node\NullableType;
use Yii;
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
            'model' => $model
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
        $system = ArrayHelper::map(System::find()->orderBy('system_name')->all(), 'system_code', 'system_name');
        
        // $abstractionData = Abstraction::find()->all();
        // $abstraction = ArrayHelper::map($abstractionData, 'id', function ($model) {
        //     return $model->table_warehouse;
        // });

        // $DWInfo = DWHelper::getDWInfoFromCache();
        

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                if ($this->request->isAjax) {
                    return $this->asJson(['status' => 'success', 'id' => $model->id]);
                }
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                // If AJAX and validation fails, return form HTML to show errors
                if ($this->request->isAjax) {
                    return $this->renderAjax('_form', ['model' => $model, 'system' => $system]);
                }
            }
        } else {
            $model->loadDefaultValues();
            if ($this->request->isAjax) {
                return $this->renderAjax('_form', ['model' => $model, 'system' => $system]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'uuid' => MyHelper::genuuid(),
            'system' => $system,
            // 'abstraction' => $abstraction,
        ]);
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

        return $this->render('update', [
            'model' => $model,
            'system' => ArrayHelper::map(System::find()->orderBy('system_name')->all(), 'system_code', 'system_name'),
            'bridgeType' => MyHelper::bridgeType(),
            'abstraction' => $abstraction,
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
