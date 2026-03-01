<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Affiliation $model */

$this->title = 'Create Affiliation';
$this->params['breadcrumbs'][] = ['label' => 'Affiliations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="affiliation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'uuid' => $uuid ?? null,
    ]) ?>

</div>
