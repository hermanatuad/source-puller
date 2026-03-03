<?php

namespace app\controllers;

use app\helpers\MyHelper;
use app\models\Abstraction;
use app\models\AbstractionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DatawarehouseController implements the CRUD actions for Abstraction model.
 */
class DatawarehouseController extends Controller
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
     * Lists all Abstraction models.
     *
     * @return string
     */
    public function actionPatient()
    {
        $searchModel = new AbstractionSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}
