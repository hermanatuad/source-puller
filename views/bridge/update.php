<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Bridge $model */

$this->title = 'Update Bridge: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Bridges', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bridge-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'system' => $system ?? null,
        'dwTables' => $dwTables ?? [],
    ]) ?>

</div>
