<?php

use app\helpers\MyHelper;
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
                <div class="col-md-4">
                    <label for="bridge-bridge_name">Pipeline Name</label>
                    <input type="form-control" class="form-control" value="<?= Html::encode($model->bridge->bridge_name) ?>" disabled>
                </div>
                <div class="col-md-4">
                    <label for="bridge-bridge_name">Warehouse Table</label>
                    <input type="form-control" class="form-control" value="<?= Html::encode($model->bridge->bridge_table_target) ?>" disabled>
                </div>
                <div class="col-md-4">
                    <label for="bridge-bridge_name">Source Table</label>
                    <input type="form-control" class="form-control" value="<?= Html::encode($model->bridge->bridge_table_source) ?>" disabled>
                </div>
            </div>
            <br>

            <div class="row g-3">

                <div class="col-md-4">
                    <?= $form->field($model, 'source_column_name')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Enter source column name',
                        'class' => 'form-control',
                        'disabled' => true
                    ])->label('Source Column Name') ?>
                </div>


                <div class="col-md-4">
                    <?= $form->field($model, 'target_column_name')->dropDownList($listColumnSource, [
                        'prompt' => 'Select target column name',
                        'class' => 'form-control',
                        'data-choices' => 'true',
                        'disabled' => true
                    ])->label('Warehouse Column Name') ?>
                </div>


                <div class="col-md-4">
                    <?= $form->field($model, 'column_type')->dropDownList(MyHelper::ColumnTypeList(), [
                        'prompt' => 'Select column type',
                        'class' => 'form-control',
                        'data-choices' => 'true',
                        'id' => 'column_type'
                    ])->label('Column Type') ?>
                </div>

                <?php if ($model->column_type === 'custom'): ?>

                    <div class="col-md-12">
                        <?= $form->field($model, 'transformation_logic')->textarea([
                            'rows' => 4,
                            'placeholder' => 'Enter transformation logic (optional) [example]: ',
                            'class' => 'form-control'
                        ]) ?>
                    </div>

                <?php endif; ?>

            </div>

        </div>
        <div class="card-footer bg-white text-end">
            <?= Html::a('<i class="ri-arrow-left-line"></i> Cancel', ['bridge/view', 'id' => $model->bridge_id], ['class' => 'btn btn-outline-secondary me-2']) ?>
            <?= Html::submitButton('<i class="ri-save-line"></i> Save', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>