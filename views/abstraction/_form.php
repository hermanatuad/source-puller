<?php

use richardfan\widget\JSRegister;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Abstraction $model */
/** @var yii\widgets\ActiveForm $form */
?>


<div class="system-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center bg-white">
            <h5 class="mb-0"><i class="ri-settings-3-line me-2"></i> System Configuration</h5>
            <small class="text-muted ms-3">Manage connection and metadata</small>
        </div>
        <div class="card-body">
            <?= $form->field($model, 'id')->hiddenInput(['value' => $uuid ?? $model->id])->label(false) ?>

            <div class="row g-3">
                <div class="col-md-12">
                    <div class="form-floating">
                        <?= $form->field($model, 'table_name')->textInput([
                            'maxlength' => true,
                            'class' => 'form-control',
                            'placeholder' => 'Patient Records'
                        ])->label('Table Name') ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating">
                        <?= $form->field($model, 'table_warehouse')->textInput([
                            'maxlength' => true,
                            'class' => 'form-control',
                            'placeholder' => 'patient_table'
                        ])->label('Table Warehouse Name') ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'type')->dropDownList([
                        'main' => 'Main',
                        'secondary' => 'Secondary',
                        'data' => 'Data'
                    ], ['prompt' => 'Select Type', 'class' => 'form-select'])->label('Abstraction Type') ?>
                </div>

                <div class="col-12">
                    <?= $form->field($model, 'description')->textarea([
                        'rows' => 3,
                        'class' => 'form-control',
                        'placeholder' => 'Optional notes about abstractions'
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