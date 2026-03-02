<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Abstraction $model */

$this->title = 'Update Abstraction: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Abstractions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="abstraction-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
