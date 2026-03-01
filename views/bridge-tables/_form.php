<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\BridgeTables $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="bridge-tables-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bridge_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_table_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'target_table_name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
