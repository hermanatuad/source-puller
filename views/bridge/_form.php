<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\helpers\MyHelper;

/** @var yii\web\View $this */
/** @var app\models\Bridge $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="bridge-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => 'bridge-form']]); ?>

    <?php
    // Ensure id exists (generate UUID client-side if not provided)
    if (empty($model->id)) {
        $model->id = MyHelper::genuuid();
    }
    echo $form->field($model, 'id')->hiddenInput()->label(false);

    // Show system_code as readonly text but submit it as hidden
    echo $form->field($model, 'system_code')->hiddenInput()->label(false);
    ?>

    <div class="mb-3">
        <label class="form-label">System Code</label>
        <div class="form-control-plaintext"><?= Html::encode($model->system_code) ?></div>
    </div>

    <?= $form->field($model, 'bridge_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bridge_source')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bridge_target')->textInput(['maxlength' => true]) ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
