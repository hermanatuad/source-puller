<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\EntitySystem $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="entity-system-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'entity_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'entity_reference')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'system_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at_data')->textInput() ?>

    <?= $form->field($model, 'updated_at_data')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
