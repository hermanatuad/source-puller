<?php

namespace app\controllers;

use app\helpers\DBHelper;
use app\helpers\MyHelper;
use app\models\System;
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
        return $this->render('view', [
            'model' => $this->findModel($id),
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
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
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
