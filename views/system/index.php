<?php

use app\models\System;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var app\models\SystemSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Database Systems';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row min-vh-100">
    <div class="col-lg-12 h-100">
        <div class="card h-100 d-flex flex-column">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="ri-user-line me-2"></i><?= Html::encode($this->title) ?>
                </h4>
                <div class="flex-shrink-0">
                    <?= Html::a('<i class="ri-add-line align-bottom me-1"></i> Add Database System', ['create'], [
                        'class' => 'btn btn-primary'
                    ]) ?>
                </div>
            </div>

            <div class="card-body d-flex flex-column flex-grow-1">
                <div class="table-responsive flex-grow-1 overflow-auto">
                    <table class="table table-hover table-striped align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">System Code</th>
                                <th scope="col">System Name</th>
                                <th scope="col">Type</th>
                                <th scope="col">Hostname</th>
                                <th scope="col">Port</th>
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
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= Html::encode($model->system_code) ?></td>
                                        <td><?= Html::a($model->system_name, ['view', 'id' => $model->id]) ?></td>
                                        <td><?= Html::encode(ucfirst($model->system_type)) ?></td>
                                        <td><?= Html::encode($model->hostname) ?></td>
                                        <td><?= Html::encode($model->port) ?></td>
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
                                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this system?')">
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
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="ri-inbox-line fs-1 d-block mb-2"></i>
                                        No systems found
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