<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\EntityAffiliation $model */

$this->title = 'Create Entity Affiliation';
$this->params['breadcrumbs'][] = ['label' => 'Entity Affiliations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="entity-affiliation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
