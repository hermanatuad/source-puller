<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\System $model */

$this->title = 'Create System';
$this->params['breadcrumbs'][] = ['label' => 'Systems', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'uuid' => $uuid ?? null,
    ]) ?>

</div>
