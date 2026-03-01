<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\System $model */

$this->title = 'Update System: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Systems', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="system-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
