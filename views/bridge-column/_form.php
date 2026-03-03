<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\BridgeColumn $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="bridge-column-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'bridge_id')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'source_column_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'target_column_name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
