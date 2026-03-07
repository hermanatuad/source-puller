<?php

namespace app\controllers;

use app\helpers\DBHelper;
use app\helpers\MyHelper;
use app\models\System;
use app\models\Affiliation;
use app\models\SystemSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SystemController implements the CRUD actions for System model.
 */
class SystemController extends Controller
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
     * Lists all System models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SystemSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single System model.
     * @param string $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $dataCache = DBHelper::getDatabaseInfoFromCache($model);
        if ($dataCache['status'] == 'success') {
            // echo '<pre>';print_r($dataCache['result']['data']);exit;
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new System model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new System();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if (empty($model->affiliation_code)) {
                    $affCode = Affiliation::find()->select('affiliation_code')->scalar();
                    $model->affiliation_code = $affCode ?: 'IJN';
                }

                try {
                    if ($model->save()) {
                        return $this->redirect(['view', 'id' => $model->id]);
                    }

                    // Validation failed; show readable errors
                    $errors = $model->getErrors();
                    $msg = [];
                    foreach ($errors as $attr => $errList) {
                        $msg[] = $attr . ': ' . implode('; ', $errList);
                    }
                    Yii::$app->session->setFlash('error', 'Failed to save System: ' . implode(' | ', $msg));
                } catch (\Throwable $e) {
                    Yii::error('System save exception: ' . $e->getMessage(), __METHOD__);
                    Yii::$app->session->setFlash('error', 'Exception while saving System: ' . $e->getMessage());
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'uuid' => MyHelper::genuuid(),
        ]);
    }

    /**
     * Updates an existing System model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing System model.
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

    public function actionCheckConnection($id)
    {
        $model = $this->findModel($id);
        if (!$model) {
            if (Yii::$app->request->isAjax) {
                return $this->asJson(['status' => 'error', 'message' => 'System not found.']);
            }

            Yii::$app->session->setFlash('error', 'System not found.');
            return $this->redirect(['index']);
        }

        $params = [
            'system_code' => $model->system_code,
            'hostname' => $model->hostname,
            'port' => $model->port,
            'username' => $model->username,
            'password' => $model->password,
            'database' => $model->database_name
        ];

        if ($model->system_type == 'mysql') {
            $connectionResult = DBHelper::testConMysql($params);

            if (Yii::$app->request->isAjax) {
                // Return JSON payload for AJAX requests
                return $this->asJson([
                    'status' => $connectionResult['status'] ?? 'error',
                    'message' => $connectionResult['message'] ?? ($connectionResult['status'] === 'success' ? 'Connection successful' : 'Connection failed'),
                    'data' => $connectionResult['data'] ?? null,
                ]);
            }

            if ($connectionResult['status'] === 'success') {
                Yii::$app->session->setFlash('success', 'Connection successful: ' . $connectionResult['message']);
            } else {
                Yii::$app->session->setFlash('error', 'Connection failed: ' . $connectionResult['message']);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            if (Yii::$app->request->isAjax) {
                return $this->asJson(['status' => 'error', 'message' => 'Unsupported system type for connection test.']);
            }

            Yii::$app->session->setFlash('error', 'Unsupported system type for connection test.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

    public function actionClearCache($id)
    {
        $model = $this->findModel($id);
        if (!$model) {
            if (Yii::$app->request->isAjax) {
                return $this->asJson(['status' => 'error', 'message' => 'System not found.']);
            }
            Yii::$app->session->setFlash('error', 'System not found.');
            return $this->redirect(['index']);
        }

        $params = [
            'hostname' => $model->hostname,
            'port' => $model->port,
            'username' => $model->username,
            'database' => $model->database_name ?? null,
        ];

        $result = DBHelper::clearCache($params);

        if (Yii::$app->request->isAjax) {
            return $this->asJson($result);
        }

        if (!empty($result['status']) && $result['status'] === 'success') {
            Yii::$app->session->setFlash('success', $result['message'] ?? 'Cache cleared');
        } else {
            Yii::$app->session->setFlash('error', $result['message'] ?? 'Failed to clear cache');
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Return sample rows for a table from the system's database.
     * @param string $id System id
     * @param string $table Table name
     * @return \yii\web\Response
     */
    public function actionTableData($id, $table)
    {
        $model = $this->findModel($id);
        if (!$model) {
            return $this->asJson(['status' => 'error', 'message' => 'System not found.']);
        }

        // Basic whitelist validation for table names to avoid injection
        if (!preg_match('/^[A-Za-z0-9_]+$/', $table)) {
            return $this->asJson(['status' => 'error', 'message' => 'Invalid table name.']);
        }

        if ($model->system_type !== 'mysql') {
            return $this->asJson(['status' => 'error', 'message' => 'Unsupported system type for table preview.']);
        }

        $hostname = $model->hostname;
        $username = $model->username;
        $password = $model->password;
        $database = $model->database_name;
        $port = $model->port;

        try {
            $mysqli = new \mysqli($hostname, $username, $password, $database, $port);
            if ($mysqli->connect_error) {
                throw new \Exception('Connection failed: ' . $mysqli->connect_error);
            }

            $safeTable = $mysqli->real_escape_string($table);
            $sql = "SELECT * FROM `" . $safeTable . "` LIMIT 50";
            $res = $mysqli->query($sql);
            if ($res === false) {
                throw new \Exception('Query error: ' . $mysqli->error);
            }

            $rows = [];
            $columns = [];
            if ($res->field_count) {
                $meta = $res->fetch_fields();
                foreach ($meta as $m) {
                    $columns[] = $m->name;
                }
            }

            // Determine which source columns are already linked for this system/table
            $linkedColumns = [];
            try {
                $bridge = \app\models\Bridge::find()->where([
                    'system_code' => $model->system_code,
                    'bridge_table_source' => $table
                ])->one();

                if ($bridge) {
                    foreach ($bridge->bridgeColumns as $bc) {
                        if (!empty($bc->source_column_name)) {
                            $linkedColumns[] = $bc->source_column_name;
                        }
                    }
                }
            } catch (\Throwable $e) {
                // ignore relation errors
            }

            $unlinked = array_values(array_diff($columns, $linkedColumns));

            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }

            $res->free();
            $mysqli->close();

            return $this->asJson(['status' => 'success', 'message' => 'OK', 'data' => ['columns' => $columns, 'rows' => $rows, 'unlinked_columns' => $unlinked]]);
        } catch (\Exception $e) {
            return $this->asJson(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Finds the System model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return System the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = System::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
