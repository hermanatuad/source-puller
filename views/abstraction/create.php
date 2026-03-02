<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Abstraction $model */

$this->title = 'Create Abstraction';
$this->params['breadcrumbs'][] = ['label' => 'Abstractions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="abstraction-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
