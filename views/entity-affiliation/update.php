<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\EntityAffiliation $model */

$this->title = 'Update Entity Affiliation: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Entity Affiliations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="entity-affiliation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
