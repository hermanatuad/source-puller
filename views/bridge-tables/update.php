<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\BridgeTables $model */

$this->title = 'Update Bridge Tables: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Bridge Tables', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bridge-tables-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
