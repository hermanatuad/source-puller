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
                    <?= $form->field($model, 'bridge_table_source')->dropDownList($model->bridge_table_source ? [$model->bridge_table_source => $model->bridge_table_source] : [], [
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

    <?php
    $getTablesUrl = Url::to(['bridge/get-tables']);
    $initialSelected = Html::encode($model->bridge_table_source ?? '');
    $this->registerJs(<<<JS
    (function(){
        var getTablesUrl = '{$getTablesUrl}';
        var sys = $('#bridge-system_code');
        var tbl = $('#bridge-bridge_table_source');

        function loadTables(systemCode, selected){
            if(!systemCode){
                tbl.html('<option value="">Select table warehouse</option>');
                return;
            }

            $.get(getTablesUrl, {system_code: systemCode}, function(res){
                tbl.empty();
                tbl.append($('<option>').val('').text('Select table warehouse'));
                if(res.status === 'success' && Array.isArray(res.tables)){
                    res.tables.forEach(function(t){
                        tbl.append($('<option>').val(t).text(t));
                    });
                    if(selected){ tbl.val(selected); }
                } else {
                    console.warn('getTables:', res);
                }
            }, 'json').fail(function(){
                console.warn('Failed to fetch tables');
            });
        }

        sys.on('change', function(){ loadTables($(this).val(), ''); });

        var initialSystem = sys.val();
        if(initialSystem){
            loadTables(initialSystem, '{$initialSelected}');
        }
    })();
    JS
    );
    ?>