<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var string $tableName */
/** @var array|null $tableData */
/** @var array $dwInfo */

$this->title = 'DW: ' . $tableName;
$this->params['breadcrumbs'][] = ['label' => 'Datawarehouse', 'url' => ['datawarehouse/index']];
$this->params['breadcrumbs'][] = $tableName;

$columns = $tableData['columns'] ?? [];
$columnsCount = $tableData['columns_count'] ?? count($columns);
$totalSize = $tableData['total_size_mb'] ?? '';

$sampleRows = [];
$sampleError = null;

// Validate simple table name (prevent injection) then attempt to fetch sample rows
if (preg_match('/^[a-zA-Z0-9_]+$/', (string)$tableName)) {
    try {
        $dsn = "pgsql:host=34.71.143.136;port=5432;dbname=datawarehouse";
        $pdo = new \PDO($dsn, 'appuser', 'AppPass!123', [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
        $stmt = $pdo->prepare('SELECT * FROM "' . $tableName . '" LIMIT 50');
        $stmt->execute();
        $sampleRows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\Throwable $e) {
        $sampleError = $e->getMessage();
    }
} else {
    $sampleError = 'Invalid table name.';
}

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="ri-table-line me-2"></i><?= Html::encode($this->title) ?>
                </h4>
                <div>
                    <?= Html::a('Back', ['datawarehouse/index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                </div>
            </div>

            <div class="card-body">
                <div class="mb-3">
                    <strong>Table:</strong> <?= Html::encode($tableName) ?>
                    <div class="text-muted small">Columns: <?= Html::encode($columnsCount) ?> • Size: <?= Html::encode($totalSize) ?> MB</div>
                </div>

                <h6>Sample Rows (up to 50)</h6>
                <?php if ($sampleError): ?>
                    <div class="alert alert-warning"><?= Html::encode($sampleError) ?></div>
                <?php elseif (empty($sampleRows)): ?>
                    <div class="alert alert-info">No rows found or empty table.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered">
                            <thead>
                            <tr>
                                <?php foreach (array_keys($sampleRows[0]) as $col): ?>
                                    <th><?= Html::encode($col) ?></th>
                                <?php endforeach; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($sampleRows as $r): ?>
                                <tr>
                                    <?php foreach ($r as $v): ?>
                                        <td><?= Html::encode((string)$v) ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
