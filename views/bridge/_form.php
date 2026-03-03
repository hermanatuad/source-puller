<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\System;

/** @var yii\web\View $this */
/** @var app\models\Bridge $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="bridge-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->hiddenInput(['value' => $uuid ?? $model->id])->label(false) ?>

    <?php $systems = ArrayHelper::map(System::find()->orderBy('system_name')->all(), 'system_code', 'system_name'); ?>
    <?= $form->field($model, 'system_code')->dropDownList($systems, ['prompt' => 'Select system']) ?>

    <?= $form->field($model, 'bridge_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bridge_source')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bridge_target')->textInput(['maxlength' => true]) ?>

    <br>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>