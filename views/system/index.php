<?php

use app\models\System;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\SystemSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Systems';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create System', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'system_code',
            'system_name',
            'description',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, System $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
