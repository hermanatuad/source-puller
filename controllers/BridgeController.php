<?php

namespace app\controllers;

use app\helpers\DBHelper;
use app\helpers\DWHelper;
use app\helpers\MyHelper;
use app\models\Bridge;
use app\models\BridgeColumn;
use app\models\BridgeSearch;
use app\models\Entity;
use app\models\EntityAffiliation;
use app\models\EntitySystem;
use app\models\System;
use Exception;
use mysqli;
use Yii;
use yii\db\mssql\PDO;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
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

        $bridgeColumnList = [];
        foreach ($bridgeColumn as $key => $value) {
            $bridgeColumnList[$value->target_column_name] = $value->source_column_name;
        }

        $bridgeColumnTypeList = [];
        foreach ($bridgeColumn as $key => $value) {
            $bridgeColumnTypeList[$value->target_column_name] = $value->column_type;
        }

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
            'bridgeColumnTypeList' => $bridgeColumnTypeList,
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
            if ($model->load($this->request->post())) {
                try {
                    if ($model->save()) {
                        return $this->redirect(['view', 'id' => $model->id]);
                    }

                    // Validation failed; flash readable errors
                    $errors = $model->getErrors();
                    $msg = [];
                    foreach ($errors as $attr => $errList) {
                        $msg[] = $attr . ': ' . implode('; ', $errList);
                    }
                    Yii::$app->session->setFlash('error', 'Failed to save Bridge: ' . implode(' | ', $msg));
                } catch (\Throwable $e) {
                    Yii::error('Bridge save exception: ' . $e->getMessage(), __METHOD__);
                    Yii::$app->session->setFlash('error', 'Exception while saving Bridge: ' . $e->getMessage());
                }
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
        $isAjax = Yii::$app->request->isAjax;
        if ($isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }
        $model = $this->findModel($id);
        $database = System::findOne(['system_code' => $model->system_code]);

        try {
            $extractedCount = 0;

            if (!$database) {
                throw new Exception("System configuration not found.");
            }

            if ($database->system_type == 'mysql') {

                if ($model->bridge_type == 'independent') {

                    $RAW_DATA = [];
                    $execute_list = [];

                    // SOURCE MYSQL

                    if (!preg_match('/^[a-zA-Z0-9_]+$/', $model->bridge_table_source)) {
                        throw new Exception("Invalid source table name.");
                    }

                    // Get primary key column from source table
                    $pkColumn = $this->getPrimaryKeyColumn(
                        $database->system_type,
                        $database->hostname,
                        $database->username,
                        $database->password,
                        $database->database_name,
                        $database->port,
                        $model->bridge_table_source
                    );

                    if (!$pkColumn) {
                        throw new Exception("Could not determine primary key for source table: {$model->bridge_table_source}");
                    }

                    $columnList = BridgeColumn::find()
                        ->select('source_column_name')
                        ->where(['bridge_id' => $id])
                        ->column();

                    if (empty($columnList)) {
                        throw new Exception("No source columns defined.");
                    }

                    $pkSourceColumn = null;
                    foreach ($columnList as $sourceCol) {
                        if (strtolower((string)$sourceCol) === strtolower((string)$pkColumn)) {
                            $pkSourceColumn = $sourceCol;
                            break;
                        }
                    }

                    if ($pkSourceColumn === null) {
                        throw new Exception("Source column '{$pkColumn}' (primary key) is required for entity mapping.");
                    }

                    $RAW_DATA = $this->fetchSourceRows($database, $model->bridge_table_source, $columnList, 100);


                    // ============================

                    // fetch to /get-data

                    // expect -> data raw dari oracle 


                    // ============================



                    if (empty($RAW_DATA)) {
                        Yii::$app->session->setFlash('info', 'No data found.');
                        return $this->redirect(['view', 'id' => $id]);
                    }

                    // FILTER EXISTING ENTITY

                    $sourceIds = array_column($RAW_DATA, $pkSourceColumn);

                    $existingReferences = EntitySystem::find()
                        ->select('entity_reference')
                        ->where([
                            'system_code' => $model->system_code,
                            'entity_reference' => $sourceIds
                        ])
                        ->column();

                    $existingMap = array_flip($existingReferences);

                    $entityRows = [];
                    $entitySystemRows = [];
                    $entityAffiliationRows = [];
                    $sourceIdToEntityId = [];

                    foreach ($RAW_DATA as $data) {

                        if (isset($existingMap[$data[$pkSourceColumn]])) {
                            continue;
                        }

                        $execute_list[] = $data;

                        $entityId = MyHelper::genEntityId();
                        $sourceIdToEntityId[$data[$pkSourceColumn]] = $entityId;
                        $uuid = MyHelper::genuuid();
                        $now = date('Y-m-d H:i:s');

                        $entityRows[] = [
                            $uuid,
                            $entityId,
                            'active',
                            'unknown',
                            $model->bridge_table_target
                        ];

                        $entitySystemRows[] = [
                            MyHelper::genuuid(),
                            $entityId,
                            $model->system_code,
                            $data[$pkSourceColumn],
                            $now,
                            $now
                        ];

                        $entityAffiliationRows[] = [
                            MyHelper::genuuid(),
                            $entityId,
                            $data[$pkSourceColumn],
                            'IJN'
                        ];
                    }

                    //  MYSQL BATCH INSERT

                    $transaction = Yii::$app->db->beginTransaction();

                    try {

                        if (!empty($entityRows)) {

                            Yii::$app->db->createCommand()->batchInsert(
                                Entity::tableName(),
                                ['id', 'entity_id', 'status', 'is_alive', 'table_target'],
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

                    // BULK INSERT POSTGRES

                    if (!empty($execute_list)) {

                        if (!preg_match('/^[a-zA-Z0-9_]+$/', $model->bridge_table_target)) {
                            throw new Exception("Invalid target table name.");
                        }

                        $bridgeCols = BridgeColumn::find()
                            ->where(['bridge_id' => $id])
                            ->all();

                        $mapTargetToSource = [];
                        $targetTypeMap = [];
                        foreach ($bridgeCols as $bc) {
                            $mapTargetToSource[$bc->target_column_name] = $bc->source_column_name;
                            $targetTypeMap[$bc->target_column_name] = $bc->column_type;
                        }

                        $pgRows = [];

                        foreach ($execute_list as $row) {
                            $mapped = [];
                            foreach ($mapTargetToSource as $targetCol => $sourceCol) {
                                $type = strtolower(trim($targetTypeMap[$targetCol] ?? ''));
                                // Accept variations like 'patient_id', 'patient id', 'patient-id'
                                if (preg_match('/patient[_\s-]?id/i', $type)) {
                                    // For patient id columns, store the generated entity_id
                                    $mapped[$targetCol] = $sourceIdToEntityId[$row[$pkSourceColumn]] ?? null;
                                } else {
                                    $mapped[$targetCol] = array_key_exists($sourceCol, $row)
                                        ? $row[$sourceCol]
                                        : null;
                                }
                            }
                            $pgRows[] = $mapped;
                        }

                        if (empty($pgRows)) {
                            Yii::$app->session->setFlash('info', 'No mapped data to insert.');
                            return $this->redirect(['view', 'id' => $id]);
                        }

                        $columns = array_keys($pgRows[0]);

                        foreach ($columns as $col) {
                            if (!preg_match('/^[a-zA-Z0-9_]+$/', $col)) {
                                throw new Exception("Invalid column name: {$col}");
                            }
                        }

                        $dsn = "pgsql:host=34.45.175.24;port=5432;dbname=datawarehouse";

                        $pdo = new \PDO($dsn, 'appuser', 'AppPass!123', [
                            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        ]);

                        // Fetch actual columns present in the target table
                        $colStmt = $pdo->prepare("SELECT column_name, character_maximum_length FROM information_schema.columns WHERE table_name = :table AND table_schema = 'public'");
                        $colStmt->execute([':table' => $model->bridge_table_target]);
                        $colInfoRows = $colStmt->fetchAll(\PDO::FETCH_ASSOC);
                        $existingCols = array_map(function ($r) {
                            return $r['column_name'];
                        }, $colInfoRows);
                        $colMaxLenMap = [];
                        foreach ($colInfoRows as $ir) {
                            $colMaxLenMap[$ir['column_name']] = isset($ir['character_maximum_length']) ? (int)$ir['character_maximum_length'] : null;
                        }

                        // filter out any target columns that do not actually exist in the target table
                        $missing = array_diff($columns, $existingCols ?: []);
                        if (!empty($missing)) {
                            Yii::warning('Missing target columns: ' . implode(', ', $missing), __METHOD__);
                            Yii::$app->session->setFlash('warning', 'Some target columns do not exist in warehouse table and will be skipped: ' . implode(', ', $missing));

                            // remove missing columns from columns list and from pgRows
                            $columns = array_values(array_intersect($columns, $existingCols ?: []));
                            if (empty($columns)) {
                                Yii::$app->session->setFlash('info', 'No valid target columns remain after filtering.');
                                return $this->redirect(['view', 'id' => $id]);
                            }
                            foreach ($pgRows as &$r) {
                                $r = array_intersect_key($r, array_flip($columns));
                            }
                            unset($r);
                        }

                        // Now build values and params based on the filtered columns
                        $values = [];
                        $params = [];
                        foreach ($pgRows as $i => $row) {
                            $placeholders = [];
                            foreach ($columns as $col) {
                                $param = ":{$col}_{$i}";
                                $placeholders[] = $param;
                                $val = $row[$col] ?? null;
                                if (is_string($val) && !empty($colMaxLenMap[$col]) && mb_strlen($val) > $colMaxLenMap[$col]) {
                                    Yii::warning("Truncating value for column {$col} from length " . mb_strlen($val) . " to {$colMaxLenMap[$col]}", __METHOD__);
                                    $val = mb_substr($val, 0, $colMaxLenMap[$col]);
                                }
                                $params[$param] = $val;
                            }
                            $values[] = "(" . implode(',', $placeholders) . ")";
                        }

                        $quotedCols = array_map(function ($c) {
                            return '"' . $c . '"';
                        }, $columns);

                        $sql = "INSERT INTO {$model->bridge_table_target} (" . implode(',', $quotedCols) . ") VALUES " . implode(',', $values);

                        $pdo->beginTransaction();

                        try {
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute($params);
                            $pdo->commit();
                            $extractedCount = count($pgRows);
                        } catch (\Throwable $e) {
                            $pdo->rollBack();
                            throw $e;
                        }
                    }
                } else if ($model->bridge_type == 'dependent') {

                    $RAW_DATA = [];
                    $execute_list = [];

                    if (!preg_match('/^[a-zA-Z0-9_]+$/', $model->bridge_table_source)) {
                        throw new Exception("Invalid source table name.");
                    }

                    // load bridge column definitions
                    $bridgeCols = BridgeColumn::find()->where(['bridge_id' => $id])->all();
                    if (empty($bridgeCols)) {
                        throw new Exception("No bridge columns defined.");
                    }

                    // collect unique source columns to select
                    $sourceCols = array_values(array_unique(array_filter(array_map(function ($bc) {
                        return $bc->source_column_name;
                    }, $bridgeCols))));

                    if (empty($sourceCols)) {
                        throw new Exception("No source columns defined.");
                    }

                    $RAW_DATA = $this->fetchSourceRows($database, $model->bridge_table_source, $sourceCols, 100);

                    if (empty($RAW_DATA)) {
                        Yii::$app->session->setFlash('info', 'No data found.');
                        return $this->redirect(['view', 'id' => $id]);
                    }

                    // identify which bridge columns are patient-id types and their source cols
                    $patientSourceCols = [];
                    $mapTargetToSource = [];
                    $targetTypeMap = [];
                    foreach ($bridgeCols as $bc) {
                        $mapTargetToSource[$bc->target_column_name] = $bc->source_column_name;
                        $targetTypeMap[$bc->target_column_name] = $bc->column_type;
                        $t = strtolower(trim((string)($bc->column_type ?? '')));
                        if (preg_match('/patient[_\s-]?id/i', $t)) {
                            $patientSourceCols[] = $bc->source_column_name;
                        }
                    }
                    $patientSourceCols = array_values(array_unique($patientSourceCols));

                    // collect all referenced source ids for patient-id columns
                    $referencedIds = [];
                    if (!empty($patientSourceCols)) {
                        foreach ($RAW_DATA as $r) {
                            foreach ($patientSourceCols as $sc) {
                                if (array_key_exists($sc, $r) && $r[$sc] !== null && $r[$sc] !== '') {
                                    $referencedIds[] = $r[$sc];
                                }
                            }
                        }
                        $referencedIds = array_values(array_unique($referencedIds));
                    }

                    // batch lookup EntitySystem to map source id -> entity_id
                    $sourceToEntity = [];
                    if (!empty($referencedIds)) {
                        $mappings = EntitySystem::find()
                            ->select(['entity_reference', 'entity_id'])
                            ->where(['system_code' => $model->system_code, 'entity_reference' => $referencedIds])
                            ->asArray()
                            ->all();
                        foreach ($mappings as $m) {
                            $sourceToEntity[$m['entity_reference']] = $m['entity_id'];
                        }
                    }

                    // build pgRows using mapped entity ids for patient-id columns
                    $pgRows = [];
                    foreach ($RAW_DATA as $row) {
                        $mapped = [];
                        foreach ($mapTargetToSource as $targetCol => $sourceCol) {
                            $type = strtolower(trim($targetTypeMap[$targetCol] ?? ''));
                            if (preg_match('/patient[_\s-]?id/i', $type)) {
                                $srcVal = array_key_exists($sourceCol, $row) ? $row[$sourceCol] : null;
                                $mapped[$targetCol] = $sourceToEntity[$srcVal] ?? null;
                            } else {
                                $mapped[$targetCol] = array_key_exists($sourceCol, $row) ? $row[$sourceCol] : null;
                            }
                        }
                        $pgRows[] = $mapped;
                    }

                    if (empty($pgRows)) {
                        Yii::$app->session->setFlash('info', 'No mapped data to insert.');
                        return $this->redirect(['view', 'id' => $id]);
                    }

                    $columns = array_keys($pgRows[0]);

                    foreach ($columns as $col) {
                        if (!preg_match('/^[a-zA-Z0-9_]+$/', $col)) {
                            throw new Exception("Invalid column name: {$col}");
                        }
                    }

                    $dsn = "pgsql:host=34.45.175.24;port=5432;dbname=datawarehouse";

                    $pdo = new \PDO($dsn, 'appuser', 'AppPass!123', [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    ]);

                    // Fetch actual columns present in the target table
                    $colStmt = $pdo->prepare("SELECT column_name, character_maximum_length FROM information_schema.columns WHERE table_name = :table AND table_schema = 'public'");
                    $colStmt->execute([':table' => $model->bridge_table_target]);
                    $colInfoRows = $colStmt->fetchAll(\PDO::FETCH_ASSOC);
                    $existingCols = array_map(function ($r) {
                        return $r['column_name'];
                    }, $colInfoRows);
                    $colMaxLenMap = [];
                    foreach ($colInfoRows as $ir) {
                        $colMaxLenMap[$ir['column_name']] = isset($ir['character_maximum_length']) ? (int)$ir['character_maximum_length'] : null;
                    }

                    // filter out any target columns that do not actually exist in the target table
                    $missing = array_diff($columns, $existingCols ?: []);
                    if (!empty($missing)) {
                        Yii::warning('Missing target columns: ' . implode(', ', $missing), __METHOD__);
                        Yii::$app->session->setFlash('warning', 'Some target columns do not exist in warehouse table and will be skipped: ' . implode(', ', $missing));

                        // remove missing columns from columns list and from pgRows
                        $columns = array_values(array_intersect($columns, $existingCols ?: []));
                        if (empty($columns)) {
                            Yii::$app->session->setFlash('info', 'No valid target columns remain after filtering.');
                            return $this->redirect(['view', 'id' => $id]);
                        }
                        foreach ($pgRows as &$r) {
                            $r = array_intersect_key($r, array_flip($columns));
                        }
                        unset($r);
                    }

                    // Now build values and params based on the filtered columns
                    $values = [];
                    $params = [];
                    foreach ($pgRows as $i => $row) {
                        $placeholders = [];
                        foreach ($columns as $col) {
                            $param = ":{$col}_{$i}";
                            $placeholders[] = $param;
                            $val = $row[$col] ?? null;
                            if (is_string($val) && !empty($colMaxLenMap[$col]) && mb_strlen($val) > $colMaxLenMap[$col]) {
                                Yii::warning("Truncating value for column {$col} from length " . mb_strlen($val) . " to {$colMaxLenMap[$col]}", __METHOD__);
                                $val = mb_substr($val, 0, $colMaxLenMap[$col]);
                            }
                            $params[$param] = $val;
                        }
                        $values[] = "(" . implode(',', $placeholders) . ")";
                    }

                    $quotedCols = array_map(function ($c) {
                        return '"' . $c . '"';
                    }, $columns);

                    $sql = "INSERT INTO {$model->bridge_table_target} (" . implode(',', $quotedCols) . ") VALUES " . implode(',', $values);

                    $pdo->beginTransaction();

                    try {
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($params);
                        $pdo->commit();
                        $extractedCount = count($pgRows);
                    } catch (\Throwable $e) {
                        $pdo->rollBack();
                        throw $e;
                    }
                } else {

                    $RAW_DATA = [];

                    if (!preg_match('/^[a-zA-Z0-9_]+$/', $model->bridge_table_source)) {
                        throw new Exception("Invalid source table name.");
                    }

                    if (!preg_match('/^[a-zA-Z0-9_]+$/', $model->bridge_table_target)) {
                        throw new Exception("Invalid target table name.");
                    }

                    $bridgeCols = BridgeColumn::find()->where(['bridge_id' => $id])->all();
                    if (empty($bridgeCols)) {
                        throw new Exception("No bridge columns defined.");
                    }

                    $mapTargetToSource = [];
                    foreach ($bridgeCols as $bc) {
                        if (!empty($bc->target_column_name) && !empty($bc->source_column_name)) {
                            $mapTargetToSource[$bc->target_column_name] = $bc->source_column_name;
                        }
                    }

                    if (empty($mapTargetToSource)) {
                        throw new Exception("No valid source-target column mapping found.");
                    }

                    $sourceCols = array_values(array_unique(array_values($mapTargetToSource)));

                    foreach ($sourceCols as $col) {
                        if (!preg_match('/^[a-zA-Z0-9_]+$/', $col)) {
                            throw new Exception("Invalid source column name: {$col}");
                        }
                    }

                    foreach (array_keys($mapTargetToSource) as $col) {
                        if (!preg_match('/^[a-zA-Z0-9_]+$/', $col)) {
                            throw new Exception("Invalid target column name: {$col}");
                        }
                    }

                    $RAW_DATA = $this->fetchSourceRows($database, $model->bridge_table_source, $sourceCols, 100);

                    if (empty($RAW_DATA)) {
                        Yii::$app->session->setFlash('info', 'No data found.');
                        return $this->redirect(['view', 'id' => $id]);
                    }

                    $pgRows = [];
                    foreach ($RAW_DATA as $row) {
                        $mapped = [];
                        foreach ($mapTargetToSource as $targetCol => $sourceCol) {
                            $mapped[$targetCol] = array_key_exists($sourceCol, $row) ? $row[$sourceCol] : null;
                        }
                        $pgRows[] = $mapped;
                    }

                    if (empty($pgRows)) {
                        Yii::$app->session->setFlash('info', 'No mapped data to insert.');
                        return $this->redirect(['view', 'id' => $id]);
                    }

                    $columns = array_keys($pgRows[0]);

                    $dsn = "pgsql:host=34.45.175.24;port=5432;dbname=datawarehouse";

                    $pdo = new \PDO($dsn, 'appuser', 'AppPass!123', [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    ]);

                    $colStmt = $pdo->prepare("SELECT column_name, character_maximum_length FROM information_schema.columns WHERE table_name = :table AND table_schema = 'public'");
                    $colStmt->execute([':table' => $model->bridge_table_target]);
                    $colInfoRows = $colStmt->fetchAll(\PDO::FETCH_ASSOC);
                    $existingCols = array_map(function ($r) {
                        return $r['column_name'];
                    }, $colInfoRows);
                    $colMaxLenMap = [];
                    foreach ($colInfoRows as $ir) {
                        $colMaxLenMap[$ir['column_name']] = isset($ir['character_maximum_length']) ? (int)$ir['character_maximum_length'] : null;
                    }

                    $missing = array_diff($columns, $existingCols ?: []);
                    if (!empty($missing)) {
                        Yii::warning('Missing target columns: ' . implode(', ', $missing), __METHOD__);
                        Yii::$app->session->setFlash('warning', 'Some target columns do not exist in warehouse table and will be skipped: ' . implode(', ', $missing));

                        $columns = array_values(array_intersect($columns, $existingCols ?: []));
                        if (empty($columns)) {
                            Yii::$app->session->setFlash('info', 'No valid target columns remain after filtering.');
                            return $this->redirect(['view', 'id' => $id]);
                        }
                        foreach ($pgRows as &$r) {
                            $r = array_intersect_key($r, array_flip($columns));
                        }
                        unset($r);
                    }

                    $values = [];
                    $params = [];
                    foreach ($pgRows as $i => $row) {
                        $placeholders = [];
                        foreach ($columns as $col) {
                            $param = ":{$col}_{$i}";
                            $placeholders[] = $param;
                            $val = $row[$col] ?? null;
                            if (is_string($val) && !empty($colMaxLenMap[$col]) && mb_strlen($val) > $colMaxLenMap[$col]) {
                                Yii::warning("Truncating value for column {$col} from length " . mb_strlen($val) . " to {$colMaxLenMap[$col]}", __METHOD__);
                                $val = mb_substr($val, 0, $colMaxLenMap[$col]);
                            }
                            $params[$param] = $val;
                        }
                        $values[] = "(" . implode(',', $placeholders) . ")";
                    }

                    $quotedCols = array_map(function ($c) {
                        return '"' . $c . '"';
                    }, $columns);

                    $sql = "INSERT INTO {$model->bridge_table_target} (" . implode(',', $quotedCols) . ") VALUES " . implode(',', $values);

                    $pdo->beginTransaction();

                    try {
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($params);
                        $pdo->commit();
                        $extractedCount = count($pgRows);
                    } catch (\Throwable $e) {
                        $pdo->rollBack();
                        throw $e;
                    }
                }

                $successMessage = "Bridge execution completed. {$extractedCount} records successfully extracted.";

                if ($isAjax) {
                    return [
                        'status' => 'success',
                        'message' => $successMessage,
                        'extractedCount' => $extractedCount,
                    ];
                }
            } elseif ($database->system_type == 'oracle') {

                if ($model->bridge_type == 'independent') {

                    $RAW_DATA = [];
                    $execute_list = [];

                    // SOURCE MYSQL

                    if (!preg_match('/^[a-zA-Z0-9_]+$/', $model->bridge_table_source)) {
                        throw new Exception("Invalid source table name.");
                    }
echo '<pre>';print_r('tes');die;
                    // Get primary key column from source table
                    $pkColumn = $this->getPrimaryKeyColumn(
                        $database->system_type,
                        $database->hostname,
                        $database->username,
                        $database->password,
                        $database->database_name,
                        $database->port,
                        $model->bridge_table_source
                    );

                    if (!$pkColumn) {
                        throw new Exception("Could not determine primary key for source table: {$model->bridge_table_source}");
                    }

                    $columnList = BridgeColumn::find()
                        ->select('source_column_name')
                        ->where(['bridge_id' => $id])
                        ->column();

                    if (empty($columnList)) {
                        throw new Exception("No source columns defined.");
                    }

                    $pkSourceColumn = null;
                    foreach ($columnList as $sourceCol) {
                        if (strtolower((string)$sourceCol) === strtolower((string)$pkColumn)) {
                            $pkSourceColumn = $sourceCol;
                            break;
                        }
                    }

                    if ($pkSourceColumn === null) {
                        throw new Exception("Source column '{$pkColumn}' (primary key) is required for entity mapping.");
                    }

                    $RAW_DATA = $this->fetchSourceRows($database, $model->bridge_table_source, $columnList, 100);


                    // ============================

                    // fetch to /get-data

                    // expect -> data raw dari oracle 


                    // ============================



                    if (empty($RAW_DATA)) {
                        Yii::$app->session->setFlash('info', 'No data found.');
                        return $this->redirect(['view', 'id' => $id]);
                    }

                    // FILTER EXISTING ENTITY

                    $sourceIds = array_column($RAW_DATA, $pkSourceColumn);

                    $existingReferences = EntitySystem::find()
                        ->select('entity_reference')
                        ->where([
                            'system_code' => $model->system_code,
                            'entity_reference' => $sourceIds
                        ])
                        ->column();

                    $existingMap = array_flip($existingReferences);

                    $entityRows = [];
                    $entitySystemRows = [];
                    $entityAffiliationRows = [];
                    $sourceIdToEntityId = [];

                    foreach ($RAW_DATA as $data) {

                        if (isset($existingMap[$data[$pkSourceColumn]])) {
                            continue;
                        }

                        $execute_list[] = $data;

                        $entityId = MyHelper::genEntityId();
                        $sourceIdToEntityId[$data[$pkSourceColumn]] = $entityId;
                        $uuid = MyHelper::genuuid();
                        $now = date('Y-m-d H:i:s');

                        $entityRows[] = [
                            $uuid,
                            $entityId,
                            'active',
                            'unknown',
                            $model->bridge_table_target
                        ];

                        $entitySystemRows[] = [
                            MyHelper::genuuid(),
                            $entityId,
                            $model->system_code,
                            $data[$pkSourceColumn],
                            $now,
                            $now
                        ];

                        $entityAffiliationRows[] = [
                            MyHelper::genuuid(),
                            $entityId,
                            $data[$pkSourceColumn],
                            'IJN'
                        ];
                    }

                    //  MYSQL BATCH INSERT

                    $transaction = Yii::$app->db->beginTransaction();

                    try {

                        if (!empty($entityRows)) {

                            Yii::$app->db->createCommand()->batchInsert(
                                Entity::tableName(),
                                ['id', 'entity_id', 'status', 'is_alive', 'table_target'],
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

                    // BULK INSERT POSTGRES

                    if (!empty($execute_list)) {

                        if (!preg_match('/^[a-zA-Z0-9_]+$/', $model->bridge_table_target)) {
                            throw new Exception("Invalid target table name.");
                        }

                        $bridgeCols = BridgeColumn::find()
                            ->where(['bridge_id' => $id])
                            ->all();

                        $mapTargetToSource = [];
                        $targetTypeMap = [];
                        foreach ($bridgeCols as $bc) {
                            $mapTargetToSource[$bc->target_column_name] = $bc->source_column_name;
                            $targetTypeMap[$bc->target_column_name] = $bc->column_type;
                        }

                        $pgRows = [];

                        foreach ($execute_list as $row) {
                            $mapped = [];
                            foreach ($mapTargetToSource as $targetCol => $sourceCol) {
                                $type = strtolower(trim($targetTypeMap[$targetCol] ?? ''));
                                // Accept variations like 'patient_id', 'patient id', 'patient-id'
                                if (preg_match('/patient[_\s-]?id/i', $type)) {
                                    // For patient id columns, store the generated entity_id
                                    $mapped[$targetCol] = $sourceIdToEntityId[$row[$pkSourceColumn]] ?? null;
                                } else {
                                    $mapped[$targetCol] = array_key_exists($sourceCol, $row)
                                        ? $row[$sourceCol]
                                        : null;
                                }
                            }
                            $pgRows[] = $mapped;
                        }

                        if (empty($pgRows)) {
                            Yii::$app->session->setFlash('info', 'No mapped data to insert.');
                            return $this->redirect(['view', 'id' => $id]);
                        }

                        $columns = array_keys($pgRows[0]);

                        foreach ($columns as $col) {
                            if (!preg_match('/^[a-zA-Z0-9_]+$/', $col)) {
                                throw new Exception("Invalid column name: {$col}");
                            }
                        }

                        $dsn = "pgsql:host=34.45.175.24;port=5432;dbname=datawarehouse";

                        $pdo = new \PDO($dsn, 'appuser', 'AppPass!123', [
                            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        ]);

                        // Fetch actual columns present in the target table
                        $colStmt = $pdo->prepare("SELECT column_name, character_maximum_length FROM information_schema.columns WHERE table_name = :table AND table_schema = 'public'");
                        $colStmt->execute([':table' => $model->bridge_table_target]);
                        $colInfoRows = $colStmt->fetchAll(\PDO::FETCH_ASSOC);
                        $existingCols = array_map(function ($r) {
                            return $r['column_name'];
                        }, $colInfoRows);
                        $colMaxLenMap = [];
                        foreach ($colInfoRows as $ir) {
                            $colMaxLenMap[$ir['column_name']] = isset($ir['character_maximum_length']) ? (int)$ir['character_maximum_length'] : null;
                        }

                        // filter out any target columns that do not actually exist in the target table
                        $missing = array_diff($columns, $existingCols ?: []);
                        if (!empty($missing)) {
                            Yii::warning('Missing target columns: ' . implode(', ', $missing), __METHOD__);
                            Yii::$app->session->setFlash('warning', 'Some target columns do not exist in warehouse table and will be skipped: ' . implode(', ', $missing));

                            // remove missing columns from columns list and from pgRows
                            $columns = array_values(array_intersect($columns, $existingCols ?: []));
                            if (empty($columns)) {
                                Yii::$app->session->setFlash('info', 'No valid target columns remain after filtering.');
                                return $this->redirect(['view', 'id' => $id]);
                            }
                            foreach ($pgRows as &$r) {
                                $r = array_intersect_key($r, array_flip($columns));
                            }
                            unset($r);
                        }

                        // Now build values and params based on the filtered columns
                        $values = [];
                        $params = [];
                        foreach ($pgRows as $i => $row) {
                            $placeholders = [];
                            foreach ($columns as $col) {
                                $param = ":{$col}_{$i}";
                                $placeholders[] = $param;
                                $val = $row[$col] ?? null;
                                if (is_string($val) && !empty($colMaxLenMap[$col]) && mb_strlen($val) > $colMaxLenMap[$col]) {
                                    Yii::warning("Truncating value for column {$col} from length " . mb_strlen($val) . " to {$colMaxLenMap[$col]}", __METHOD__);
                                    $val = mb_substr($val, 0, $colMaxLenMap[$col]);
                                }
                                $params[$param] = $val;
                            }
                            $values[] = "(" . implode(',', $placeholders) . ")";
                        }

                        $quotedCols = array_map(function ($c) {
                            return '"' . $c . '"';
                        }, $columns);

                        $sql = "INSERT INTO {$model->bridge_table_target} (" . implode(',', $quotedCols) . ") VALUES " . implode(',', $values);

                        $pdo->beginTransaction();

                        try {
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute($params);
                            $pdo->commit();
                            $extractedCount = count($pgRows);
                        } catch (\Throwable $e) {
                            $pdo->rollBack();
                            throw $e;
                        }
                    }
                }
            }

            Yii::$app->session->setFlash('success', $successMessage);
            return $this->redirect(['index']);
            // return $this->redirect(['view', 'id' => $id]);
        } catch (\Throwable $e) {
            Yii::error('Bridge run failed: ' . $e->getMessage(), __METHOD__);

            if ($isAjax) {
                return [
                    'status' => 'error',
                    'message' => 'Bridge execution failed: ' . $e->getMessage(),
                    'extractedCount' => 0,
                ];
            }

            Yii::$app->session->setFlash('error', 'Bridge execution failed: ' . $e->getMessage());
            return $this->redirect(['index']);
        }
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

        $system = ArrayHelper::map(System::find()->orderBy('system_name')->all(), 'system_code', 'system_name');
        $DWInfo = DWHelper::getDWInfoFromCache();

        $dwTables = array_keys($DWInfo['result']['data']['tables']);
        $dwTables  = array_combine($dwTables, $dwTables);


        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'system' => $system,
            'dwTables' => $dwTables,
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
        $model = $this->findModel($id);

        $bridgeColumns = BridgeColumn::find()->where(['bridge_id' => $id])->all();
        foreach ($bridgeColumns as $bc) {
            $bc->delete();
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Get primary key column name for supported source systems
     * @param string $systemType
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database_name
     * @param int $port
     * @param string $table_name
     * @return string|null Primary key column name or null if not found
     */
    protected function getPrimaryKeyColumn($systemType, $hostname, $username, $password, $database_name, $port, $table_name)
    {
        $type = strtolower((string)$systemType);

        try {
            if ($type === 'mysql') {
                $mysqli = new mysqli($hostname, $username, $password, $database_name, $port);
                if ($mysqli->connect_error) {
                    Yii::error('MySQL connection failed: ' . $mysqli->connect_error, __METHOD__);
                    return null;
                }

                $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = '" . $mysqli->real_escape_string($table_name) . "' 
                        AND COLUMN_KEY = 'PRI'";

                $result = $mysqli->query($sql);
                if (!$result) {
                    Yii::error('MySQL PK query error: ' . $mysqli->error, __METHOD__);
                    $mysqli->close();
                    return null;
                }

                $row = $result->fetch_assoc();
                $mysqli->close();

                return $row ? $row['COLUMN_NAME'] : null;
            }

            if ($type === 'oracle') {
                $oraclePort = !empty($port) ? $port : 1521;
                $dsn = "oci:dbname=//{$hostname}:{$oraclePort}/{$database_name};charset=AL32UTF8";
                $pdo = new \PDO($dsn, $username, $password, [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                ]);

                $stmt = $pdo->prepare(
                    "SELECT ucc.column_name
                     FROM user_constraints uc
                     JOIN user_cons_columns ucc ON uc.constraint_name = ucc.constraint_name
                     WHERE uc.constraint_type = 'P'
                       AND uc.table_name = :table_name
                     ORDER BY ucc.position"
                );
                $stmt->execute([':table_name' => strtoupper($table_name)]);
                $row = $stmt->fetch(\PDO::FETCH_ASSOC);

                return $row['COLUMN_NAME'] ?? null;
            }

            Yii::error('Unsupported system type for PK lookup: ' . $systemType, __METHOD__);
            return null;
        } catch (\Exception $e) {
            Yii::error('Error getting primary key: ' . $e->getMessage(), __METHOD__);
            return null;
        }
    }

    /**
     * Fetch source rows from MySQL or Oracle using selected columns.
     * Returned row keys follow the same case as requested $sourceCols.
     * @param System $database
     * @param string $tableName
     * @param array $sourceCols
     * @param int $limit
     * @return array
     * @throws Exception
     */
    protected function fetchSourceRows(System $database, $tableName, array $sourceCols, $limit = 100)
    {
        $systemType = strtolower((string)($database->system_type ?? ''));

        if (!preg_match('/^[a-zA-Z0-9_]+$/', (string)$tableName)) {
            throw new Exception("Invalid source table name.");
        }

        foreach ($sourceCols as $col) {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', (string)$col)) {
                throw new Exception("Invalid source column name: {$col}");
            }
        }

        if ($systemType === 'mysql') {
            $mysqli = new mysqli(
                $database->hostname,
                $database->username,
                $database->password,
                $database->database_name,
                $database->port
            );

            if ($mysqli->connect_error) {
                throw new Exception("MySQL connection failed: " . $mysqli->connect_error);
            }

            $escapedColumns = array_map(function ($col) {
                return "`" . $col . "`";
            }, $sourceCols);

            $sql = "SELECT " . implode(',', $escapedColumns) . " FROM `{$tableName}` LIMIT " . (int)$limit;
            $result = $mysqli->query($sql);

            if (!$result) {
                throw new Exception("MySQL query error: " . $mysqli->error);
            }

            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }

            $result->free();
            $mysqli->close();
            return $rows;
        }

        if ($systemType === 'oracle') {
            $oraclePort = !empty($database->port) ? $database->port : 1521;
            $dsn = "oci:dbname=//{$database->hostname}:{$oraclePort}/{$database->database_name};charset=AL32UTF8";
            $pdo = new \PDO($dsn, $database->username, $database->password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
            ]);

            $selectCols = implode(',', $sourceCols);
            $sql = "SELECT {$selectCols} FROM {$tableName} WHERE ROWNUM <= :row_limit";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':row_limit', (int)$limit, \PDO::PARAM_INT);
            $stmt->execute();
            $rawRows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $normalizedRows = [];
            foreach ($rawRows as $row) {
                $lowerCaseMap = array_change_key_case($row, CASE_LOWER);
                $normalized = [];
                foreach ($sourceCols as $col) {
                    $normalized[$col] = $lowerCaseMap[strtolower($col)] ?? null;
                }
                $normalizedRows[] = $normalized;
            }

            return $normalizedRows;
        }

        throw new Exception("Unsupported source system type for run: {$database->system_type}");
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
            } elseif (strpos($systemType, 'oracle') !== false) {
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
