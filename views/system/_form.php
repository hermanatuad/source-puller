<?php

use richardfan\widget\JSRegister;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\System $model */
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
                <div class="col-md-4">
                    <div class="form-floating">
                        <?= $form->field($model, 'system_code')->textInput([
                            'maxlength' => true,
                            'class' => 'form-control',
                            'placeholder' => 'SYS001'
                        ])->label('System Code') ?>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="form-floating">
                        <?= $form->field($model, 'system_name')->textInput([
                            'maxlength' => true,
                            'class' => 'form-control',
                            'placeholder' => 'My Application'
                        ])->label('System Name') ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'system_type')->dropDownList([
                        'mysql' => 'MySQL',
                        'postgres' => 'PostgreSQL'
                    ], ['prompt' => 'Select Type', 'class' => 'form-select'])->label('System Type') ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'username')->textInput([
                        'maxlength' => true,
                        'class' => 'form-control',
                        'id' => 'system-username',
                        'placeholder' => 'admin'
                    ])->label('Username') ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'password')->passwordInput([
                        'maxlength' => true,
                        'class' => 'form-control',
                        'id' => 'system-password',
                        'placeholder' => '••••••••'
                    ])->label('Password') ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'hostname')->textInput([
                        'maxlength' => true,
                        'class' => 'form-control',
                        'placeholder' => 'host.example.com or 192.168.1.10'
                    ])->label('Hostname') ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'port')->input('number', [
                        'class' => 'form-control',
                        'min' => 1,
                        'max' => 65535,
                        'placeholder' => '3306'
                    ])->label('Port') ?>
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

    <?php JSRegister::begin(); ?>
    <script>
        document.getElementById('toggle-system-password')?.addEventListener('click', function() {
            var pwd = document.getElementById('system-password');
            if (!pwd) return;
            if (pwd.type === 'password') {
                pwd.type = 'text';
                this.innerText = 'Hide';
            } else {
                pwd.type = 'password';
                this.innerText = 'Show';
            }
        });
    </script>
    <?php JSRegister::end(); ?>

</div>

<?php JSRegister::begin(); ?>
<script>

</script>
<?php JSRegister::end(); ?>