<?php

use app\models\Entity;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var app\models\EntitySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Entities';
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
                </div>
            </div>

            <div class="card-body d-flex flex-column flex-grow-1">
                <div class="table-responsive flex-grow-1 overflow-auto">
                    <table class="table table-hover table-striped align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Entity ID</th>
                                <th scope="col">Life Status</th>
                                <th scope="col">Affiliations</th>
                                <th scope="col">Sources</th>
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
                                        <td> <?= Html::a(Html::encode($model->entity_id), ['view', 'id' => $model->id]) ?></td>
                                        <td><?= Html::encode($model->is_alive) ?></td>
                                        <td>
                                            <?php if (!empty($model->affiliations)): ?>
                                                <?php foreach ($model->affiliations as $aff): ?>
                                                    <?= Html::tag('span', Html::encode($aff->affiliation_code), ['class' => 'badge bg-secondary me-1']) ?>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($model->systems)): ?>
                                                <?php foreach ($model->systems as $sys): ?>
                                                    <?= Html::tag('span', Html::encode($sys->system_code), ['class' => 'badge bg-info text-dark me-1', 'title' => Html::encode($sys->entity_reference)]) ?>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="ri-inbox-line fs-1 d-block mb-2"></i>
                                        No entity found
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