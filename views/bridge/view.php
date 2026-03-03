<?php

use app\models\BridgeColumn;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Bridge $model */
$table_warehouse = \app\models\Abstraction::findOne(['id' => $model->bridge_target])->table_warehouse ?? 'N/A';
$this->title = '[' . $model->system_code . '] ' . $model->bridge_source . ' -> ' . $table_warehouse;
$this->params['breadcrumbs'][] = ['label' => 'Bridges', 'url' => ['index']];
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
            'bridge_type',
            'bridge_source',
            [
                'label' => 'Abstraction Table',
                'value' => function ($model) {
                    $abstraction = \app\models\Abstraction::findOne(['id' => $model->bridge_target]);
                    return $abstraction ? $abstraction->table_warehouse : 'N/A';
                },
            ],
            'created_at',
            'updated_at',
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
                <div class="row">
                    <div class="col-md-4">
                        Requirements
                    </div>
                    <div class="col-md-4">
                        Sources
                    </div>
                    <div class="col-md-4">
                        Actions
                    </div>

                    <div class="col-md-12">

                        <table class="table table-borderless mb-0">
                            <tbody>
                                <?php foreach ($abstractionColumn as $key => $value) : 
                                    
                                    $bridgeColumn = BridgeColumn::find()->where(['bridge_id' => $model->id, 'target_column_name' => $value->column_warehouse])->one();
                                    ?>
                                    <tr>
                                        <th scope="row" style="width: 33%;">
                                            <i class="ri-hashtag me-2 text-muted"></i><?= $value->column_warehouse ?>
                                        </th>
                                        <td style="width: 33%;">
                                            <?= $bridgeColumn->source_column_name ?? 'N/A' ?>
                                        </td>
                                        <td style="width: 33%;">
                                            <?=  Html::a('Edit', ['bridge-column/update', 'bridge_id' => $model->id, 'target_column_name' => $value->column_warehouse], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>