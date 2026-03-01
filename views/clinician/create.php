<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Clinician $model */

$this->title = 'Create Clinician';
$this->params['breadcrumbs'][] = ['label' => 'Clinicians', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clinician-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
