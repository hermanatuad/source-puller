<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Clinician $model */

$this->title = 'Update Clinician: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Clinicians', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="clinician-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
