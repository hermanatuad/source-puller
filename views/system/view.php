<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\BridgeSearch;

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

        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>Bridges for this System</div>
                <?= Html::a('Add Bridge', ['bridge/create', 'system_code' => $model->system_code], ['class' => 'btn btn-success btn-sm']) ?>
            </div>
            <div class="card-body p-0">
                <?php
                $searchModel = new BridgeSearch();
                $dataProvider = $searchModel->search(array_merge(Yii::$app->request->queryParams, ['BridgeSearch' => ['system_code' => $model->system_code]]));
                ?>

                <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'summary' => false,
                    'tableOptions' => ['class' => 'table table-hover align-middle mb-0'],
                    'columns' => [
                        ['class' => 'yii\\grid\\SerialColumn'],
                        'bridge_type',
                        'bridge_source',
                        'bridge_target',
                        'created_at:datetime',
                        ['class' => 'yii\\grid\\ActionColumn', 'controller' => 'bridge'],
                    ],
                ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>