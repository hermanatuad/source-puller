<?php

use app\helpers\DBHelper;
use app\helpers\DWHelper;
use app\helpers\MyHelper;
use app\models\BridgeColumn;
use app\models\System;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Bridge $model */

$system = System::find()->where(['system_code' => $model->system_code])->one();
$this->title = $model->bridge_name;
$this->params['breadcrumbs'][] = ['label' => 'Systems', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $system->system_name, 'url' => ['system/view', 'id' => $system->id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="bridge-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="text-end mb-3">
        <?= Html::a('<i class="ri-arrow-left-line"></i> Back', ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="ri-delete-bin-2-line"></i> Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'system_code',
            'bridge_table_source',
            'bridge_table_target',
            [
                'attribute' => 'bridge_type',
                'value' => function ($model) {
                    return ucfirst($model->bridge_type);
                }
            ],
            'created_at:datetime',
        ],
    ]) ?>

</div>

<div class="row">

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="ri-server-line me-2"></i> Extraction Pipeline Columns
                </h4>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Column Sources</th>
                                <th>Column Warehouse</th>
                                <th>Column Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $system = System::find()->where(['system_code' => $model->system_code])->one();
                            $dbInfo = DBHelper::getDatabaseInfoFromCache($system);
                            $columns = $dbInfo['result']['tables'][$model->bridge_table_source]['columns'] ?? [];
                            if (!empty($columns)):
                            ?>
                                <?php foreach ($columns as $column): ?>
                                    <?php
                                    $colName = $column['name'] ?? null;
                                    $isLinked = isset($bridgeColumnList[$colName]) && !empty($bridgeColumnList[$colName]);
                                    echo '<pre>';print_r($isLinked);exit;
                                    $rowClass = $isLinked ? 'table-success' : 'table-warning';
                                    ?>
                                    <tr class="<?= $rowClass ?>">
                                        <td><?= $bridgeColumnList[$colName] ?? 'N/A' ?></td>
                                        <td><?= Html::encode($colName ?: 'N/A') ?></td>
                                        <td><?= MyHelper::ColumnTypeList()[$bridgeColumnTypeList[$colName] ?? ''] ?? 'N/A' ?></td>
                                        <td>
                                            <?= Html::a('<i class="ri-edit-2-line"></i>', ['bridge-column/update', 'bridge_id' => $model->id, 'target_column_name' => $colName], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                                            <?= Html::a('<i class="ri-delete-bin-2-line"></i>', ['bridge-column/delete', 'bridge_id' => $model->id, 'target_column_name' => $colName], [
                                                'class' => 'btn btn-sm btn-outline-danger',
                                                'data' => [
                                                    'confirm' => 'Are you sure you want to delete this item?',
                                                    'method' => 'post',
                                                ],
                                            ]) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted">No permissions assigned</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="ri-server-line me-2"></i> Columns Source
                </h4>
            </div>
            <div class="card-body">
                <?php
                $dbInfo = DBHelper::getDatabaseInfoFromCache($system);
                $sourceColumns = $dbInfo['result']['tables'][$model->bridge_table_source]['columns'] ?? [];
                $linkedSourceCols = array_filter(array_values($bridgeColumnList));
                ?>

                <div class="table-responsive">
                    <table class="table table-hover align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Source Column</th>
                                <th>Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($sourceColumns)): ?>
                                <?php foreach ($sourceColumns as $col):
                                    $colName = $col['name'] ?? null;
                                    $isLinked = in_array($colName, $linkedSourceCols, true);
                                    $rowClass = $isLinked ? 'table-success' : 'table-danger';
                                ?>
                                    <tr class="<?= $rowClass ?>">
                                        <td><?= Html::encode($colName ?: '-') ?></td>
                                        <td><?= Html::encode($col['data_type'] ?? $col['column_type'] ?? '-') ?></td>
                                        <td>
                                            <?php if ($isLinked): ?>
                                                <span class="badge bg-success">Linked</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Unlinked</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No source columns available or unable to fetch schema</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>