<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\models\BridgeSearch;

/** @var yii\web\View $this */
/** @var app\models\System $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Systems', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="system-view">

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
            'system_name',
            'system_type',
            'hostname',
            'password',
            'port',
            'path',
            'description',
        ],
    ]) ?>

    <div class="mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>Bridges for this System</div>
                <?= Html::a('Add Bridge', ['bridge/create', 'system_code' => $model->system_code], ['class' => 'btn btn-success btn-sm']) ?>
            </div>
            <div class="card-body p-0">
                <?php
                $searchModel = new BridgeSearch();
                $dataProvider = $searchModel->search(array_merge(Yii::$app->request->queryParams, ['BridgeSearch' => ['system_code' => $model->system_code]]));
                ?>

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'summary' => false,
                    'columns' => [
                        ['class' => 'yii\\grid\\SerialColumn'],
                        'bridge_type',
                        'bridge_source',
                        'bridge_target',
                        'created_at',
                        ['class' => 'yii\\grid\\ActionColumn', 'controller' => 'bridge'],
                    ],
                ]) ?>
            </div>
        </div>
    </div>

</div>
