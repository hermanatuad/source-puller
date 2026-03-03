<?php

namespace app\controllers;

use app\helpers\DWHelper;
use app\helpers\MyHelper;
use app\models\Abstraction;
use app\models\AbstractionColumn;
use app\models\Bridge;
use app\models\BridgeSearch;
use app\models\System;
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
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $abstractionColumn = AbstractionColumn::findAll(['abstraction_id' => $model->bridge_target]);   
        return $this->render('view', [
            'model' => $model,
            'abstractionColumn' => $abstractionColumn,
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
        $abstractionData = Abstraction::find()->all();
        $abstraction = ArrayHelper::map($abstractionData, 'id', function ($model) {
            return $model->table_warehouse;
        });

        $DWInfo = DWHelper::getDWInfoFromCache();
        echo '<pre>';print_r($DWInfo);exit;

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
            'bridgeType' => MyHelper::bridgeType(),
            'abstraction' => $abstraction,
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
}
