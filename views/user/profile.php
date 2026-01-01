<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = 'My Profile';
$this->params['breadcrumbs'][] = $this->title;

// Get auth manager for role checking
$auth = Yii::$app->authManager;
$roles = $auth->getRolesByUser($model->id);
$roleNames = array_keys($roles);
$roleName = !empty($roleNames) ? $roleNames[0] : 'No Role';

// Get role badge color
$roleBadgeClass = 'bg-secondary';
if ($roleName === 'creator') {
    $roleBadgeClass = 'bg-danger';
} elseif ($roleName === 'admin') {
    $roleBadgeClass = 'bg-warning';
} elseif ($roleName === 'user') {
    $roleBadgeClass = 'bg-success';
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="ri-user-settings-line me-2"></i><?= Html::encode($this->title) ?>
                </h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3 text-center border-end">
                        <div class="avatar-xl mx-auto mb-3">
                            <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-1">
                                <?= strtoupper(substr($model->username, 0, 2)) ?>
                            </div>
                        </div>
                        <h5 class="mb-1"><?= Html::encode($model->name) ?></h5>
                        <p class="text-muted mb-2">@<?= Html::encode($model->username) ?></p>
                        <span class="badge <?= $roleBadgeClass ?> mb-3">
                            <?= Html::encode($roleName) ?>
                        </span>
                        <?php if ($model->status == 10): ?>
                            <br><span class="badge bg-success-subtle text-success">Active</span>
                        <?php else: ?>
                            <br><span class="badge bg-danger-subtle text-danger">Inactive</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-9">
                        <div class="p-3">
                            <?php $form = ActiveForm::begin([
                                'id' => 'profile-form',
                                'options' => ['class' => 'needs-validation'],
                            ]); ?>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <?= $form->field($model, 'username')->textInput([
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter username',
                                            'maxlength' => true,
                                            'readonly' => true,
                                        ])->hint('Username cannot be changed') ?>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <?= $form->field($model, 'email')->textInput([
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter email',
                                            'type' => 'email',
                                            'maxlength' => true,
                                        ]) ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <?= $form->field($model, 'name')->textInput([
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter full name',
                                            'maxlength' => true,
                                        ]) ?>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <?= $form->field($model, 'telegram_id')->textInput([
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter Telegram ID',
                                            'maxlength' => true,
                                        ]) ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <?= $form->field($model, 'phone_number')->textInput([
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter phone number',
                                            'maxlength' => true,
                                        ]) ?>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <?= $form->field($model, 'whatsapp_number')->textInput([
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter WhatsApp number',
                                            'maxlength' => true,
                                        ]) ?>
                                    </div>
                                </div>
                            </div>

                            <div class="card border border-warning mt-4">
                                <div class="card-header bg-warning-subtle">
                                    <h5 class="card-title mb-0">
                                        <i class="ri-lock-password-line me-2"></i>Change Password
                                        <span class="badge bg-info ms-2">Optional</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <?= $form->field($model, 'password')->passwordInput([
                                                    'class' => 'form-control',
                                                    'placeholder' => 'Leave blank to keep current password',
                                                    'id' => 'user-password',
                                                ]) ?>
                                                <small class="text-muted">Leave blank if you don't want to change password. Minimum 6 characters.</small>
                                                <div class="password-strength mt-2" id="password-strength" style="display: none;">
                                                    <div class="progress" style="height: 5px;">
                                                        <div class="progress-bar" role="progressbar" id="password-strength-bar"></div>
                                                    </div>
                                                    <small class="text-muted" id="password-strength-text"></small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="user-password-confirm">Confirm Password</label>
                                                <input type="password" class="form-control <?= $model->hasErrors('password_confirm') ? 'is-invalid' : '' ?>" id="user-password-confirm" name="User[password_confirm]" placeholder="Re-enter new password" value="<?= Html::encode($model->password_confirm) ?>">
                                                <?php if ($model->hasErrors('password_confirm')): ?>
                                                    <div class="invalid-feedback d-block">
                                                        <?= Html::error($model, 'password_confirm') ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="invalid-feedback" id="password-confirm-error">Passwords do not match</div>
                                                    <div class="valid-feedback" id="password-confirm-success">Passwords match</div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <?= Html::submitButton('<i class="ri-save-line align-bottom me-1"></i> Update Profile', [
                                    'class' => 'btn btn-primary'
                                ]) ?>
                                <?= Html::a('<i class="ri-arrow-left-line align-bottom me-1"></i> Cancel', ['site/index'], [
                                    'class' => 'btn btn-secondary ms-2'
                                ]) ?>
                            </div>

                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
$(document).ready(function() {
    const passwordInput = $('#user-password');
    const confirmInput = $('#user-password-confirm');
    const strengthBar = $('#password-strength-bar');
    const strengthText = $('#password-strength-text');
    const strengthContainer = $('#password-strength');
    
    // Password strength indicator
    passwordInput.on('input', function() {
        const password = $(this).val();
        
        if (password.length === 0) {
            strengthContainer.hide();
            return;
        }
        
        strengthContainer.show();
        
        let strength = 0;
        let text = '';
        let barClass = '';
        
        // Calculate strength
        if (password.length >= 6) strength += 25;
        if (password.length >= 10) strength += 25;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 25;
        if (/[0-9]/.test(password)) strength += 15;
        if (/[^a-zA-Z0-9]/.test(password)) strength += 10;
        
        // Set text and color
        if (strength < 30) {
            text = 'Weak';
            barClass = 'bg-danger';
        } else if (strength < 60) {
            text = 'Fair';
            barClass = 'bg-warning';
        } else if (strength < 80) {
            text = 'Good';
            barClass = 'bg-info';
        } else {
            text = 'Strong';
            barClass = 'bg-success';
        }
        
        // Update UI
        strengthBar.removeClass('bg-danger bg-warning bg-info bg-success').addClass(barClass);
        strengthBar.css('width', strength + '%');
        strengthText.text('Password strength: ' + text);
        
        // Check match when password changes
        checkPasswordMatch();
    });
    
    // Check password match
    function checkPasswordMatch() {
        const password = passwordInput.val();
        const confirm = confirmInput.val();
        
        if (confirm.length === 0) {
            confirmInput.removeClass('is-invalid is-valid');
            return true;
        }
        
        if (password === confirm) {
            confirmInput.removeClass('is-invalid').addClass('is-valid');
            return true;
        } else {
            confirmInput.removeClass('is-valid').addClass('is-invalid');
            return false;
        }
    }
    
    // Validate on confirm password input
    confirmInput.on('input', function() {
        checkPasswordMatch();
    });
    
    // Form validation
    $('#profile-form').on('beforeSubmit', function(e) {
        const password = passwordInput.val();
        const confirm = confirmInput.val();
        
        // Check password length if password is filled
        if (password.length > 0 && password.length < 6) {
            alert('Password must be at least 6 characters long');
            passwordInput.focus();
            return false;
        }
        
        // Check password match if password is filled
        if (password.length > 0 && password !== confirm) {
            alert('Passwords do not match');
            confirmInput.focus();
            return false;
        }
        
        return true;
    });
});
JS
);
?>
