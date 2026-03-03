<?php

use app\helpers\DBHelper;
use app\helpers\DWHelper;
use app\models\BridgeColumn;
use app\models\System;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Bridge $model */

$system = System::find()->where(['system_code' => $model->system_code])->one();
$this->title = '[' . $model->system_code . '] ' . $model->bridge_table_source . ' x datawarehouse';
$this->params['breadcrumbs'][] = ['label' => 'Systems', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $system->system_name, 'url' => ['system/view', 'id' => $system->id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="bridge-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
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
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>


<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="ri-server-line me-2"></i> Brige Column
                </h4>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Column Warehouse</th>
                                <th>Column Sources</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $dwInfo = DWHelper::getDWInfoFromCache();
                            $columns = $dwInfo['result']['data']['tables'][$model->bridge_table_target]['columns'] ?? [];
                            if (!empty($columns)):
                            ?>
                                <?php foreach ($columns as $column): ?>
                                    <tr>
                                        <td>
                                            <?= Html::encode($column['name'] ?: 'N/A') ?></td>
                                        </td>
                                        <td>
                                            <?= $bridgeColumnList[$column['name']] ?? 'N/A' ?>
                                        </td>
                                        <td>
                                            <?= Html::a('Config', ['bridge-column/view', 'bridge_id' => $model->id, 'target_column_name' => $column['name']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
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
</div>