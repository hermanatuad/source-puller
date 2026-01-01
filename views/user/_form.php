<?php

use richardfan\widget\JSRegister;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var yii\widgets\ActiveForm $form */

// Get all roles from RBAC
$authManager = Yii::$app->authManager;
$roles = $authManager->getRoles();
$rolesList = ArrayHelper::map($roles, 'name', 'description');

// Get current user's assigned roles
$assignedRoles = [];
if (!$model->isNewRecord) {
    $userRoles = $authManager->getRolesByUser($model->id);
    $assignedRoles = array_keys($userRoles);
}
?>

<?php $form = ActiveForm::begin([
    'id' => 'user-form',
    'options' => ['class' => 'needs-validation'],
]); ?>

<div class="row">
    <div class="col-lg-6">
        <div class="mb-3">
            <?= $form->field($model, 'username')->textInput([
                'class' => 'form-control',
                'placeholder' => 'Enter username',
                'maxlength' => true,
            ]) ?>
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
            <?php if ($model->isNewRecord): ?>
                <?= $form->field($model, 'password')->passwordInput([
                    'class' => 'form-control',
                    'placeholder' => 'Enter password',
                    'id' => 'user-password',
                ]) ?>
                <small class="text-muted">Minimum 6 characters</small>
                <div class="password-strength mt-2" id="password-strength" style="display: none;">
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar" role="progressbar" id="password-strength-bar"></div>
                    </div>
                    <small class="text-muted" id="password-strength-text"></small>
                </div>
            <?php else: ?>
                <?= $form->field($model, 'password')->passwordInput([
                    'class' => 'form-control',
                    'placeholder' => 'Leave blank to keep current password',
                    'id' => 'user-password',
                ]) ?>
                <small class="text-muted">Leave blank if you don't want to change password</small>
                <div class="password-strength mt-2" id="password-strength" style="display: none;">
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar" role="progressbar" id="password-strength-bar"></div>
                    </div>
                    <small class="text-muted" id="password-strength-text"></small>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="mb-3">
            <?php if ($model->isNewRecord): ?>
                <label class="form-label" for="user-password-confirm">Confirm Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control <?= $model->hasErrors('password_confirm') ? 'is-invalid' : '' ?>" id="user-password-confirm" name="User[password_confirm]" placeholder="Re-enter password" value="<?= Html::encode($model->password_confirm) ?>">
                <?php if ($model->hasErrors('password_confirm')): ?>
                    <div class="invalid-feedback d-block">
                        <?= Html::error($model, 'password_confirm') ?>
                    </div>
                <?php else: ?>
                    <div class="invalid-feedback" id="password-confirm-error">Passwords do not match</div>
                    <div class="valid-feedback" id="password-confirm-success">Passwords match</div>
                <?php endif; ?>
            <?php else: ?>
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
            <?php endif; ?>
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
            <?= $form->field($model, 'status')->dropDownList([
                \app\models\User::STATUS_ACTIVE => 'Active',
                \app\models\User::STATUS_INACTIVE => 'Inactive',
            ], ['class' => 'form-select', 'prompt' => 'Select Status']) ?>
        </div>
    </div>
</div>

<?php JSRegister::begin(); ?>
<script>
    $(document).ready(function() {
        const passwordInput = $('#user-password');
        const confirmInput = $('#user-password-confirm');
        const strengthBar = $('#password-strength-bar');
        const strengthText = $('#password-strength-text');
        const strengthContainer = $('#password-strength');
        const confirmError = $('#password-confirm-error');
        const confirmSuccess = $('#password-confirm-success');

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
        $('#user-form').on('beforeSubmit', function(e) {
            const password = passwordInput.val();
            const confirm = confirmInput.val();
            const isNewRecord = <?= $model->isNewRecord ? 'true' : 'false' ?>;

            // Check password length
            if (isNewRecord && password.length < 6) {
                alert('Password must be at least 6 characters long');
                passwordInput.focus();
                return false;
            }

            if (!isNewRecord && password.length > 0 && password.length < 6) {
                alert('Password must be at least 6 characters long');
                passwordInput.focus();
                return false;
            }

            // Check password match
            if (isNewRecord && password !== confirm) {
                alert('Passwords do not match');
                confirmInput.focus();
                return false;
            }

            if (!isNewRecord && password.length > 0 && password !== confirm) {
                alert('Passwords do not match');
                confirmInput.focus();
                return false;
            }

            return true;
        });
    });
</script>
<?php JSRegister::end(); ?>

<div class="row">
    <div class="col-lg-4">
        <div class="mb-3">
            <?= $form->field($model, 'telegram_id')->textInput([
                'class' => 'form-control',
                'placeholder' => 'Enter Telegram ID',
                'maxlength' => true,
            ]) ?>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="mb-3">
            <?= $form->field($model, 'phone_number')->textInput([
                'class' => 'form-control',
                'placeholder' => 'Enter phone number',
                'maxlength' => true,
            ]) ?>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="mb-3">
            <?= $form->field($model, 'whatsapp_number')->textInput([
                'class' => 'form-control',
                'placeholder' => 'Enter WhatsApp number',
                'maxlength' => true,
            ]) ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="mb-3">
            <?= $form->field($model, 'access_role')->textInput([
                'class' => 'form-control',
                'placeholder' => 'Enter access role (optional)',
                'maxlength' => true,
            ]) ?>
            <small class="text-muted">Additional role information (optional)</small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border border-primary">
            <div class="card-header bg-primary-subtle">
                <h5 class="card-title mb-0">
                    <i class="ri-shield-user-line me-2"></i>RBAC Role Assignment
                    <?php if ($model->isNewRecord): ?>
                        <span class="badge bg-info ms-2">Optional</span>
                    <?php endif; ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">
                        <?= $model->isNewRecord ? 'Assign RBAC Role (Optional)' : 'Change RBAC Role' ?>
                    </label>
                    <?= Html::dropDownList('rbac_role', $assignedRoles[0] ?? null, $rolesList, [
                        'class' => 'form-select',
                        'prompt' => 'Select RBAC Role',
                        'id' => 'rbac-role-select'
                    ]) ?>
                    <small class="text-muted">
                        <?php if ($model->isNewRecord): ?>
                            Select a role to assign to this new user. If not selected, no role will be assigned.
                        <?php else: ?>
                            This will replace all existing roles and assign the selected role to the user.
                        <?php endif; ?>
                    </small>
                </div>

                <?php if (!$model->isNewRecord && !empty($assignedRoles)): ?>
                    <div class="alert alert-info mb-0">
                        <strong>Current RBAC Role:</strong>
                        <?php foreach ($assignedRoles as $role): ?>
                            <span class="badge bg-primary"><?= Html::encode($role) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <?= Html::submitButton('<i class="ri-save-line align-bottom me-1"></i> Save', [
        'class' => 'btn btn-primary'
    ]) ?>
    <?= Html::a('<i class="ri-close-line align-bottom me-1"></i> Cancel', ['index'], [
        'class' => 'btn btn-secondary ms-2'
    ]) ?>
</div>

<?php ActiveForm::end(); ?>