<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Patients $model */

$this->title = 'Create Patients';
$this->params['breadcrumbs'][] = ['label' => 'Patients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="patients-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
