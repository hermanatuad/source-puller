<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\EntitySystem $model */

$this->title = 'Create Entity System';
$this->params['breadcrumbs'][] = ['label' => 'Entity Systems', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="entity-system-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
