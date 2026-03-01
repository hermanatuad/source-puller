<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ClinicianSystem $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="clinician-system-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'clinician_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'clinician_reference')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'system_code')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
