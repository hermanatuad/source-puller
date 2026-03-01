<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ClinicianSystem $model */

$this->title = 'Update Clinician System: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Clinician Systems', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="clinician-system-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
