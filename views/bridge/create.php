<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Bridge $model */

$this->title = 'Create Bridge';
$this->params['breadcrumbs'][] = ['label' => 'Bridges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bridge-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
