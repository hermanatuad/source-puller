<?php

namespace app\controllers;

use app\helpers\MyHelper;
use app\models\AbstractionColumn;
use app\models\AbstractionColumnSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AbstractionColumnController implements the CRUD actions for AbstractionColumn model.
 */
class AbstractionColumnController extends Controller
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
     * Lists all AbstractionColumn models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AbstractionColumnSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AbstractionColumn model.
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
     * Creates a new AbstractionColumn model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new AbstractionColumn();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionAjaxCreate()
    {
        $request = \Yii::$app->request;
        if (!$request->isAjax || !$request->isPost) {
            throw new \yii\web\BadRequestHttpException('Invalid request');
        }

        $model = new AbstractionColumn();
        $model->id = MyHelper::genuuid();
        $model->abstraction_id = $request->post('abstraction_id');
        $model->column_type = $request->post('column_type');
        $model->column_warehouse = $request->post('column_warehouse');
        $model->description = $request->post('description');

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($model->save()) {
            return ['status' => 'success'];
        }

        return ['status' => 'error', 'errors' => $model->getErrors()];
    }

    /**
     * Updates an existing AbstractionColumn model.
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
     * Deletes an existing AbstractionColumn model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect(['abstraction/view', 'id' => $model->abstraction_id]);
    }

    /**
     * Finds the AbstractionColumn model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return AbstractionColumn the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AbstractionColumn::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
