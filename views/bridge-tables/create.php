<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\BridgeTables $model */

$this->title = 'Create Bridge Tables';
$this->params['breadcrumbs'][] = ['label' => 'Bridge Tables', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bridge-tables-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
