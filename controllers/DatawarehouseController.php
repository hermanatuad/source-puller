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