<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

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
                    <i class="ri-user-line me-2"></i>User Details
                </h4>
                <div class="flex-shrink-0">
                    <?= Html::a('<i class="ri-pencil-line align-bottom me-1"></i> Update', ['update', 'id' => $model->id], [
                        'class' => 'btn btn-primary btn-sm me-2'
                    ]) ?>
                    <?= Html::beginForm(['delete', 'id' => $model->id], 'post', ['style' => 'display:inline']) ?>
                        <button type="submit" class="btn btn-danger btn-sm me-2" onclick="return confirm('Are you sure you want to delete this user?')">
                            <i class="ri-delete-bin-line align-bottom me-1"></i> Delete
                        </button>
                    <?= Html::endForm() ?>
                    <?= Html::a('<i class="ri-arrow-left-line align-bottom me-1"></i> Back', ['index'], [
                        'class' => 'btn btn-secondary btn-sm'
                    ]) ?>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center border-end">
                            <div class="avatar-xl mx-auto mb-3">
                                <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-1">
                                    <?= strtoupper(substr($model->username, 0, 2)) ?>
                                </div>
                            </div>
                            <h5 class="mb-1"><?= Html::encode($model->name) ?></h5>
                            <p class="text-muted mb-2"><?= Html::encode($model->username) ?></p>
                            <span class="badge <?= $roleBadgeClass ?> mb-3">
                                <?= Html::encode($roleName) ?>
                            </span>
                            <?php if ($model->status == 10): ?>
                                <br><span class="badge bg-success-subtle text-success">Active</span>
                            <?php else: ?>
                                <br><span class="badge bg-danger-subtle text-danger">Inactive</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width: 200px;">
                                        <i class="ri-hashtag me-2 text-muted"></i>ID
                                    </th>
                                    <td><?= Html::encode($model->id) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-user-line me-2 text-muted"></i>Username
                                    </th>
                                    <td><?= Html::encode($model->username) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-mail-line me-2 text-muted"></i>Email
                                    </th>
                                    <td><?= Html::encode($model->email) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-account-circle-line me-2 text-muted"></i>Name
                                    </th>
                                    <td><?= Html::encode($model->name) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-telegram-line me-2 text-muted"></i>Telegram ID
                                    </th>
                                    <td><?= Html::encode($model->telegram_id) ?: '<span class="text-muted">-</span>' ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-phone-line me-2 text-muted"></i>Phone Number
                                    </th>
                                    <td><?= Html::encode($model->phone_number) ?: '<span class="text-muted">-</span>' ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-whatsapp-line me-2 text-muted"></i>WhatsApp Number
                                    </th>
                                    <td><?= Html::encode($model->whatsapp_number) ?: '<span class="text-muted">-</span>' ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-shield-user-line me-2 text-muted"></i>Access Role
                                    </th>
                                    <td><?= Html::encode($model->access_role) ?: '<span class="text-muted">-</span>' ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-shield-check-line me-2 text-muted"></i>RBAC Role
                                    </th>
                                    <td>
                                        <span class="badge <?= $roleBadgeClass ?>">
                                            <?= Html::encode($roleName) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-time-line me-2 text-muted"></i>Created At
                                    </th>
                                    <td><?= Yii::$app->formatter->asDatetime($model->created_at) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-refresh-line me-2 text-muted"></i>Updated At
                                    </th>
                                    <td><?= Yii::$app->formatter->asDatetime($model->updated_at) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (!empty($roles)): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-shield-star-line me-2"></i>RBAC Permissions
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Permission Name</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $permissions = $auth->getPermissionsByUser($model->id);
                            if (!empty($permissions)): 
                            ?>
                                <?php foreach ($permissions as $permission): ?>
                                    <tr>
                                        <td>
                                            <i class="ri-key-2-line me-2 text-muted"></i>
                                            <?= Html::encode($permission->name) ?>
                                        </td>
                                        <td><?= Html::encode($permission->description ?: '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted">No permissions assigned</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
