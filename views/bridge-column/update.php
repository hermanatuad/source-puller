<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\BridgeColumn $model */

$this->title = 'Update Bridge Column: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Bridge Columns', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bridge-column-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
