<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Affiliation $model */

$this->title = 'Update Affiliation: ' . $model->affiliation_name;
$this->params['breadcrumbs'][] = ['label' => 'Affiliations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="affiliation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
