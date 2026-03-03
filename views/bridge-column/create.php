<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\BridgeColumn $model */

$this->title = 'Create Bridge Column';
$this->params['breadcrumbs'][] = ['label' => 'Bridge Columns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bridge-column-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
