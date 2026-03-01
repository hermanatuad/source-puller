<?php

use app\models\System;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\SystemSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Systems';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="ri-user-line me-2"></i><?= Html::encode($this->title) ?>
                </h4>
                <div class="flex-shrink-0">
                    <?= Html::a('<i class="ri-add-line align-bottom me-1"></i> Add System', ['create'], [
                        'class' => 'btn btn-primary'
                    ]) ?>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Username</th>
                                <th scope="col">Email</th>
                                <th scope="col">Name</th>
                                <th scope="col">Status</th>
                                <th scope="col">RBAC Role</th>
                                <th scope="col">Created At</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($dataProvider->getCount() > 0): ?>
                                <?php 
                                $pageSize = $dataProvider->pagination->pageSize;
                                $page = $dataProvider->pagination->page;
                                $no = $page * $pageSize + 1;
                                ?>
                                <?php foreach ($dataProvider->getModels() as $model): ?>
                                    <?php 
                                    // Get user's RBAC roles
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
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    <div class="avatar-xs">
                                                        <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-13">
                                                            <?= strtoupper(substr($model->username, 0, 1)) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="fs-14 mb-0">
                                                        <?= Html::a(Html::encode($model->username), ['view', 'id' => $model->id], [
                                                            'class' => 'text-body'
                                                        ]) ?>
                                                    </h5>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= Html::encode($model->email) ?></td>
                                        <td><?= Html::encode($model->name) ?></td>
                                        <td>
                                            <?php if ($model->status == 10): ?>
                                                <span class="badge bg-success-subtle text-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge <?= $roleBadgeClass ?>">
                                                <?= Html::encode($roleName) ?>
                                            </span>
                                        </td>
                                        <td><?= Yii::$app->formatter->asDatetime($model->created_at) ?></td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-fill"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <?= Html::a('<i class="ri-eye-fill align-bottom me-2 text-muted"></i> View', ['view', 'id' => $model->id], [
                                                            'class' => 'dropdown-item'
                                                        ]) ?>
                                                    </li>
                                                    <li>
                                                        <?= Html::a('<i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit', ['update', 'id' => $model->id], [
                                                            'class' => 'dropdown-item'
                                                        ]) ?>
                                                    </li>
                                                    <li class="dropdown-divider"></li>
                                                    <li>
                                                        <?= Html::beginForm(['delete', 'id' => $model->id], 'post', ['style' => 'display:inline']) ?>
                                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                                                <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                            </button>
                                                        <?= Html::endForm() ?>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="ri-inbox-line fs-1 d-block mb-2"></i>
                                        No users found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($dataProvider->pagination->pageCount > 1): ?>
                    <div class="row mt-3">
                        <div class="col-sm-12 col-md-5">
                            <div class="dataTables_info">
                                Showing <?= $dataProvider->getCount() ?> of <?= $dataProvider->getTotalCount() ?> entries
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="dataTables_paginate paging_simple_numbers float-end">
                                <?= LinkPager::widget([
                                    'pagination' => $dataProvider->pagination,
                                    'options' => ['class' => 'pagination'],
                                    'linkOptions' => ['class' => 'page-link'],
                                    'activePageCssClass' => 'active',
                                    'disabledPageCssClass' => 'disabled',
                                    'prevPageLabel' => '<i class="mdi mdi-chevron-left"></i>',
                                    'nextPageLabel' => '<i class="mdi mdi-chevron-right"></i>',
                                ]) ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>