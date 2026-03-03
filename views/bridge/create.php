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

    <?php
    $prefillSystem = Yii::$app->request->get('system_code');
    if (!empty($prefillSystem) && empty($model->system_code)) {
        $model->system_code = $prefillSystem;
    }
    ?>

    <?= $this->render('_form', [
        'model' => $model,
        'uuid' => $uuid ?? null,
        'system' => $system ?? null,
        'bridgeType' => $bridgeType ?? null,
        'abstractionColumn' => $abstractionColumn ?? null,
    ]) ?>

</div>
