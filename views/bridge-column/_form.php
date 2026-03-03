<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\BridgeColumn $model */
/** @var yii\widgets\ActiveForm $form */
?>


<div class="bridge-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center bg-white">
            <h5 class="mb-0"><i class="ri-settings-3-line me-2"></i> Bridge Configuration</h5>
            <small class="text-muted ms-3">Manage connection and metadata</small>
        </div>
        <div class="card-body">
            <?= $form->field($model, 'id')->hiddenInput(['value' => $uuid ?? $model->id])->label(false) ?>
            <?= $form->field($model, 'bridge_id')->hiddenInput(['maxlength' => true])->label(false) ?>

            <div class="row g-3">

                <div class="col-md-6">
                    <?= $form->field($model, 'target_column_name')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Enter target column name',
                        'class' => 'form-control',
                        'disabled' => true
                    ]) ?>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'source_column_name')->dropDownList($listColumnSource, [
                        'prompt' => 'Select source column name',
                        'class' => 'form-control',
                        'id' => 'source_column_name'
                    ])->label('Column Source') ?>
                </div>

            </div>
        </div>
        <div class="card-footer bg-white text-end">
            <?= Html::a('<i class="ri-arrow-left-line"></i> Cancel', ['bridge/view', 'id' => $model->bridge_id], ['class' => 'btn btn-outline-secondary me-2']) ?>
            <?= Html::submitButton('<i class="ri-save-line"></i> Save', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>