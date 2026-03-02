<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\AbstractionColumn $model */

$this->title = 'Create Abstraction Column';
$this->params['breadcrumbs'][] = ['label' => 'Abstraction Columns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="abstraction-column-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
