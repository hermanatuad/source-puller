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

    <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center bg-white">
            <h5 class="mb-0"><i class="ri-settings-3-line me-2"></i> Bridge Configuration</h5>
            <small class="text-muted ms-3">Manage connection and metadata</small>
        </div>
        <div class="card-body">
            <?= $form->field($model, 'id')->hiddenInput(['value' => $uuid ?? $model->id])->label(false) ?>

            <div class="row g-3">

                <div class="col-md-12">
                    <?= $form->field($model, 'bridge_name')->textInput(['placeholder' => 'Enter bridge name']) ?>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'system_code')->dropDownList($system, [
                        'prompt' => 'Select system',
                        'class' => 'form-control',
                        'data-choices' => 'true',
                        'id' => 'bridge-system_code'
                    ]) ?>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'bridge_table_source')->dropDownList([], [
                        'prompt' => 'Select table warehouse',
                        'class' => 'form-control',
                        'data-choices' => 'true',
                        'id' => 'bridge-bridge_table_source'
                    ])->label('Table Warehouse') ?>
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
    $('#bridge-system_code').on('change', function() {
        var systemCode = $(this).val();

        $.ajax({
            url: '<?= Url::to(['bridge/get-tables']) ?>',
            data: {
                system_code: systemCode
            },
            success: function(response) {

                if (response.status === 'success') {

                    var selectElement = document.getElementById('bridge-bridge_table_source');

                    var choicesInstance = selectElement.choices;

                    if (!choicesInstance) {
                        console.error('Choices instance not found');
                        return;
                    }

                    choicesInstance.clearChoices();
                    choicesInstance.clearStore();

                    var newChoices = response.tables.map(function(table) {
                        return {
                            value: table,
                            label: table
                        };
                    });

                    choicesInstance.setChoices(newChoices, 'value', 'label', true);
                }
            }
        });
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>