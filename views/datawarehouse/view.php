<?php

use yii\helpers\Html;
use yii\data\Pagination;
use yii\widgets\LinkPager;

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
$pagination = null;

// Validate simple table name (prevent injection) then attempt to fetch sample rows
if (preg_match('/^[a-zA-Z0-9_]+$/', (string)$tableName)) {
    try {
        $pageSize = 50;
        $offset = 0;

        $dsn = "pgsql:host=34.45.175.24;port=5432;dbname=datawarehouse";
        $pdo = new \PDO($dsn, 'appuser', 'AppPass!123', [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);

        $countStmt = $pdo->prepare('SELECT COUNT(*) AS total_rows FROM "' . $tableName . '"');
        $countStmt->execute();
        $totalRows = (int)($countStmt->fetchColumn() ?: 0);

        $pagerParams = Yii::$app->request->getQueryParams();
        $pagerParams['table'] = $tableName;
        unset($pagerParams['tableName']);

        $pagination = new Pagination([
            'totalCount' => $totalRows,
            'defaultPageSize' => $pageSize,
            'pageSize' => $pageSize,
            'route' => 'datawarehouse/view',
            'params' => $pagerParams,
            'pageParam' => 'page',
            'pageSizeParam' => false,
            'forcePageParam' => true,
            'validatePage' => false,
        ]);

        $offset = $pagination->offset;
        $pageSize = $pagination->getLimit();

        $stmt = $pdo->prepare('SELECT * FROM "' . $tableName . '" LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $pageSize, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
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

                <h6>Sample Rows (50 per page)</h6>
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

                    <?php if ($pagination !== null && $pagination->totalCount > $pagination->pageSize): ?>
                        <?php
                        $currentPage = $pagination->getPage() + 1;
                        $totalPages = (int)ceil($pagination->totalCount / $pagination->pageSize);
                        ?>
                        <div class="mt-3 pt-2 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div class="text-muted small">
                                Showing <?= Html::encode((string)($offset + 1)) ?>-
                                <?= Html::encode((string)min($offset + count($sampleRows), $pagination->totalCount)) ?>
                                of <?= Html::encode((string)$pagination->totalCount) ?> rows
                                • Page <?= Html::encode((string)$currentPage) ?> of <?= Html::encode((string)$totalPages) ?>
                            </div>
                            <?= LinkPager::widget([
                                'pagination' => $pagination,
                                'options' => ['class' => 'pagination pagination-sm mb-0'],
                                'linkContainerOptions' => ['class' => 'page-item'],
                                'linkOptions' => ['class' => 'page-link'],
                                'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link'],
                                'firstPageLabel' => 'First',
                                'lastPageLabel' => 'Last',
                                'prevPageLabel' => 'Prev',
                                'nextPageLabel' => 'Next',
                                'maxButtonCount' => 7,
                            ]) ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
