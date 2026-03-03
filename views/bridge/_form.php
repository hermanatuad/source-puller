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

    <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center bg-white">
            <h5 class="mb-0"><i class="ri-settings-3-line me-2"></i> Bridge Configuration</h5>
            <small class="text-muted ms-3">Manage connection and metadata</small>
        </div>
        <div class="card-body">
            <?= $form->field($model, 'id')->hiddenInput(['value' => $uuid ?? $model->id])->label(false) ?>

            <div class="row g-3">

                <div class="col-md-6">
                    <?= $form->field($model, 'bridge_source')->dropDownList($systems, [
                        'prompt' => 'Select system',
                        'class' => 'form-control',
                    ]) ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'bridge_type')->textInput([
                        'maxlength' => true,
                        'class' => 'form-control',
                        'id' => 'bridge-type',
                        'placeholder' => 'Bridge Type'
                    ])->label('Bridge Type') ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'bridge_target')->input('number', [
                        'class' => 'form-control',
                        'placeholder' => 'Bridge Target'
                    ])->label('Bridge Target') ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'database_name')->textInput([
                        'maxlength' => true,
                        'class' => 'form-control',
                        'placeholder' => 'Database Name'
                    ])->label('Database Name') ?>
                </div>

                <div class="col-md-8">
                    <?= $form->field($model, 'path')->hiddenInput([
                        'maxlength' => true,
                        'class' => 'form-control',
                        'placeholder' => '/api/v1'
                    ])->label(false) ?>
                </div>

                <div class="col-12">
                    <?= $form->field($model, 'description')->textarea([
                        'rows' => 3,
                        'class' => 'form-control',
                        'placeholder' => 'Optional notes about this system'
                    ])->label('Description') ?>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white text-end">
            <?= Html::a('<i class="ri-arrow-left-line"></i> Cancel', ['index'], ['class' => 'btn btn-outline-secondary me-2']) ?>
            <?= Html::submitButton('<i class="ri-save-line"></i> Save', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>