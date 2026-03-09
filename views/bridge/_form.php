<?php

use app\models\Abstraction;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\System;

/** @var yii\web\View $this */
/** @var app\models\Bridge $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="bridge-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger">
            <?= Html::encode(Yii::$app->session->getFlash('error')) ?>
        </div>
    <?php endif; ?>

    <?= $form->errorSummary($model) ?>

    <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center bg-white">
            <h5 class="mb-0"><i class="ri-settings-3-line me-2"></i> Bridge Configuration</h5>
            <small class="text-muted ms-3">Manage connection and metadata</small>
        </div>
        <div class="card-body">
            <?= $form->field($model, 'id')->hiddenInput(['value' => $uuid ?? $model->id])->label(false) ?>

            <div class="row g-3">

                <div class="col-md-8">
                    <?= $form->field($model, 'bridge_name')->textInput(['placeholder' => 'Enter bridge name']) ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'system_code')->dropDownList($system, [
                        'prompt' => 'Select system',
                        'class' => 'form-control',
                        'data-choices' => 'true',
                        'id' => 'bridge-system_code'
                    ])->label('Database Source') ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'bridge_table_source')->dropDownList([], [
                        'prompt' => 'Select table source',
                        'class' => 'form-control',
                        'id' => 'bridge-bridge_table_source'
                    ])->label('Table Source') ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'bridge_table_target')->dropDownList($dwTables, [
                        'prompt' => 'Select table warehouse',
                        'class' => 'form-control',
                        'data-choices' => 'true',
                        'id' => 'bridge-bridge_table_target'
                    ])->label('Table Warehouse') ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'bridge_type')->dropDownList([
                        'independent' => 'Independent',
                        'dependent' => 'Dependent'
                    ], [
                        'prompt' => 'Select bridge type',
                        'class' => 'form-control',
                        'data-choices' => 'true',
                        'id' => 'bridge-bridge_type'
                    ])->label('Pipeline Type') ?>
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

<?php \richardfan\widget\JSRegister::begin(); ?>
<script>
    var tableChoices = new Choices('#bridge-bridge_table_source', {
        allowHTML: false
    });
    $('#bridge-system_code').on('change', function() {
        var systemCode = $(this).val();

        $.ajax({
            url: '<?= Url::to(['bridge/get-tables']) ?>',
            data: {
                system_code: systemCode
            },
            success: function(response) {

                if (response.status === 'success') {

                    tableChoices.clearChoices();

                    var newChoices = response.tables.map(function(table) {
                        return {
                            value: table,
                            label: table
                        };
                    });

                    tableChoices.setChoices(newChoices, 'value', 'label', true);
                }
            }
        });
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>