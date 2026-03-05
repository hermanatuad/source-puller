<?php

namespace app\controllers;

use app\helpers\DWHelper;
use Yii;
use yii\web\Controller;

class DatawarehouseController extends Controller
{
    /**
     * Lists datawarehouse tables
     */
    public function actionIndex()
    {
        $DWInfo = DWHelper::getDWInfoFromCache();
        return $this->render('index', [
            'dwInfo' => $DWInfo,
        ]);
    }

    /**
     * Shows a single DW table and its columns
     * @param string $table
     */
    public function actionView($table)
    {
        $DWInfo = DWHelper::getDWInfoFromCache();
        $tables = $DWInfo['result']['data']['tables'] ?? [];
        $tableName = (string)$table;
        $tableData = $tables[$tableName] ?? null;

        return $this->render('view', [
            'tableName' => $tableName,
            'tableData' => $tableData,
            'dwInfo' => $DWInfo,
        ]);
    }
}
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
