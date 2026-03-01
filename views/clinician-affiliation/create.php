<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ClinicianAffiliation $model */

$this->title = 'Create Clinician Affiliation';
$this->params['breadcrumbs'][] = ['label' => 'Clinician Affiliations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clinician-affiliation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
