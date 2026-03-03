<?php

use app\helpers\DBHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\BridgeSearch;
use richardfan\widget\JSRegister;

/** @var yii\web\View $this */
/** @var app\models\System $model */

$this->title = $model->system_name ?: $model->system_code;
$this->params['breadcrumbs'][] = ['label' => 'Systems', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="ri-server-line me-2"></i>System Details
                </h4>
                <div class="flex-shrink-0">
                    <?= Html::a('<i class="ri-pencil-line align-bottom me-1"></i> Update', ['update', 'id' => $model->id], [
                        'class' => 'btn btn-primary btn-sm me-2'
                    ]) ?>
                    <?= Html::beginForm(['delete', 'id' => $model->id], 'post', ['style' => 'display:inline']) ?>
                    <button type="submit" class="btn btn-danger btn-sm me-2" onclick="return confirm('Are you sure you want to delete this system?')">
                        <i class="ri-delete-bin-line align-bottom me-1"></i> Delete
                    </button>
                    <?= Html::endForm() ?>
                    <?= Html::a('<i class="ri-file-text-line align-bottom me-1"></i> Test Connection', ['check-connection', 'id' => $model->id], [
                        'class' => 'btn btn-info btn-sm me-2'
                    ]) ?>
                    <?= Html::a('<i class="ri-refresh-line align-bottom me-1"></i> Clear Cache', ['clear-cache', 'id' => $model->id], [
                        'class' => 'btn btn-secondary btn-sm me-2'
                    ]) ?>
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
                                    <?= Html::encode(strtoupper(substr($model->system_code, 0, 2))) ?>
                                </div>
                            </div>
                            <h5 class="mb-1"><?= Html::encode($model->system_name) ?></h5>
                            <p class="text-muted mb-2"><?= Html::encode($model->system_code) ?></p>
                            <span class="badge bg-info-subtle text-info mb-3">
                                <?= Html::encode($model->system_type) ?>
                            </span>
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
                                        <i class="ri-code-s-slash-line me-2 text-muted"></i>System Code
                                    </th>
                                    <td><?= Html::encode($model->system_code) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-code-s-slash-line me-2 text-muted"></i>Database Name
                                    </th>
                                    <td><?= Html::encode($model->database_name) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-key-2-line me-2 text-muted"></i>Hostname
                                    </th>
                                    <td><?= Html::encode($model->hostname) ?: '<span class="text-muted">-</span>' ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-user-line me-2 text-muted"></i>Username
                                    </th>
                                    <td><?= Html::encode($model->username) ?: '<span class="text-muted">-</span>' ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-lock-line me-2 text-muted"></i>Password
                                    </th>
                                    <td><?= !empty($model->password) ? '<span class="text-muted">••••••</span>' : '<span class="text-muted">-</span>' ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-voiceprint-line me-2 text-muted"></i>Port
                                    </th>
                                    <td><?= Html::encode($model->port) ?: '<span class="text-muted">-</span>' ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-folder-line me-2 text-muted"></i>Path
                                    </th>
                                    <td><?= Html::encode($model->path) ?: '<span class="text-muted">-</span>' ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-file-text-line me-2 text-muted"></i>Description
                                    </th>
                                    <td><?= Html::encode($model->description) ?: '<span class="text-muted">-</span>' ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-bridge" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalgridLabel">Grid Modals</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="javascript:void(0);">
                            <div class="row g-3">
                                <div class="col-xxl-6">
                                    <div>
                                        <label for="firstName" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="firstName" placeholder="Enter firstname">
                                    </div>
                                </div><!--end col-->
                                <div class="col-xxl-6">
                                    <div>
                                        <label for="lastName" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lastName" placeholder="Enter lastname">
                                    </div>
                                </div><!--end col-->
                                <div class="col-lg-12">
                                    <label for="genderInput" class="form-label">Gender</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">
                                            <label class="form-check-label" for="inlineRadio1">Male</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                            <label class="form-check-label" for="inlineRadio2">Female</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3">
                                            <label class="form-check-label" for="inlineRadio3">Others</label>
                                        </div>
                                    </div>
                                </div><!--end col-->
                                <div class="col-xxl-6">
                                    <div>
                                        <label for="emailInput" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="emailInput" placeholder="Enter your email">
                                    </div>
                                </div><!--end col-->
                                <div class="col-xxl-6">
                                    <div>
                                        <label for="passwordInput" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="passwordInput" value="451326546">
                                    </div>
                                </div><!--end col-->
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div><!--end col-->
                            </div><!--end row-->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="ri-server-line me-2"></i>Data Sources
                </h4>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Status</th>
                                <th>Table Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $tables = DBHelper::getDatabaseInfoFromCache($model);
                            echo '<pre>';print_r($tables);exit;
                            if (!empty($tables)):
                            ?>
                                <?php foreach ($tables as $table): ?>
                                    <tr>
                                        <td>
                                            <i class="ri-key-2-line me-2 text-muted"></i>
                                            <?= Html::encode($table->name) ?>
                                        </td>
                                        <td><?= Html::encode($table->description ?: '-') ?></td>
                                        <td>
                                            <?= Html::a('Edit', ['update', 'id' => $table->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                                        </td>
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

    </div>
</div>