<?php

use app\models\Bridge;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var app\models\BridgeSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Extraction Pipelines';
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
                    <?= Html::a('<i class="ri-add-line align-bottom me-1"></i> Add Extraction Pipeline', ['create'], [
                        'class' => 'btn btn-primary'
                    ]) ?>
                </div>
            </div>

            <div class="card-body d-flex flex-column flex-grow-1">
                <?php foreach (['success' => 'success', 'error' => 'danger', 'warning' => 'warning', 'info' => 'info'] as $flashKey => $flashClass): ?>
                    <?php if (Yii::$app->session->hasFlash($flashKey)): ?>
                        <div class="alert alert-<?= $flashClass ?> alert-dismissible fade show" role="alert">
                            <?= Html::encode(Yii::$app->session->getFlash($flashKey)) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <div class="table-responsive flex-grow-1 overflow-auto">
                    <table class="table table-hover table-striped align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Pipeline Name</th>
                                <th scope="col">Warehouse Table</th>
                                <th scope="col">Database Source</th>
                                <th scope="col">Table Source</th>
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
                                        <td> <?= Html::a(Html::encode($model->bridge_name), ['view', 'id' => $model->id]) ?></td>
                                        <td><?= Html::encode($model->bridge_table_target) ?></td>
                                        <td><?= Html::encode($model->system_code) ?></td>
                                        <td><?= Html::encode($model->bridge_table_source) ?></td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-fill"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <button
                                                            type="button"
                                                            class="dropdown-item run-btn"
                                                            data-run-url="<?= Url::to(['run', 'id' => $model->id]) ?>"
                                                            data-bridge-name="<?= Html::encode($model->bridge_name ?: $model->bridge_table_source) ?>">
                                                            <i class="ri-play-line align-bottom me-2 text-primary"></i> Run
                                                        </button>



                                                    </li>
                                                    <li class="dropdown-divider"></li>
                                                    <li>
                                                        <?= Html::a('<i class="ri-eye-fill align-bottom me-2 text-muted"></i> View', ['view', 'id' => $model->id], [
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

                    <div class="modal fade" id="runConfirmModal" tabindex="-1" aria-labelledby="runConfirmModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="runConfirmModalLabel">Konfirmasi Run Pipeline</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="runConfirmForm" method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                                        <p class="mb-2">Anda yakin ingin menjalankan pipeline berikut?</p>
                                        <div class="fw-semibold" id="runPipelineName">-</div>
                                        <div id="runExecutionResult" class="alert mt-3 mb-0 d-none" role="alert"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary" id="runConfirmSubmitBtn">
                                            <span class="spinner-border spinner-border-sm me-2 d-none" id="runLoadingSpinner" role="status" aria-hidden="true"></span>
                                            <span id="runSubmitText">Ya, Jalankan</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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


<?php
$this->registerJs(<<<JS
(function () {
    const modalElement = document.getElementById('runConfirmModal');
    if (!modalElement) {
        return;
    }

    const modal = new bootstrap.Modal(modalElement);
    const runButtons = document.querySelectorAll('.run-btn');
    const runForm = document.getElementById('runConfirmForm');
    const pipelineNameElement = document.getElementById('runPipelineName');
    const submitButton = document.getElementById('runConfirmSubmitBtn');
    const submitText = document.getElementById('runSubmitText');
    const spinner = document.getElementById('runLoadingSpinner');
    const resultAlert = document.getElementById('runExecutionResult');

    runButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            runForm.setAttribute('action', button.dataset.runUrl || '#');
            pipelineNameElement.textContent = button.dataset.bridgeName || '-';
            submitButton.disabled = false;
            submitButton.dataset.mode = 'run';
            spinner.classList.add('d-none');
            submitText.textContent = 'Ya, Jalankan';
            resultAlert.classList.add('d-none');
            resultAlert.classList.remove('alert-success', 'alert-danger', 'alert-warning', 'alert-info');
            resultAlert.textContent = '';
            modal.show();
        });
    });

    runForm.addEventListener('submit', function (event) {
        event.preventDefault();

        if (submitButton.dataset.mode === 'done') {
            modal.hide();
            return;
        }

        submitButton.disabled = true;
        spinner.classList.remove('d-none');
        submitText.textContent = 'Running...';

        const formData = new FormData(runForm);

        fetch(runForm.getAttribute('action'), {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                const isSuccess = data && data.status === 'success';
                resultAlert.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning', 'alert-info');
                resultAlert.classList.add(isSuccess ? 'alert-success' : 'alert-danger');

                if (isSuccess) {
                    resultAlert.textContent = data.message || 'Bridge execution completed.';
                    submitText.textContent = 'Selesai';
                    submitButton.dataset.mode = 'done';
                } else {
                    resultAlert.textContent = (data && data.message) ? data.message : 'Terjadi kesalahan saat menjalankan pipeline.';
                    submitText.textContent = 'Coba Lagi';
                    submitButton.dataset.mode = 'run';
                }
            })
            .catch(function () {
                resultAlert.classList.remove('d-none', 'alert-success', 'alert-warning', 'alert-info');
                resultAlert.classList.add('alert-danger');
                resultAlert.textContent = 'Terjadi kesalahan jaringan saat menjalankan pipeline.';
                submitText.textContent = 'Coba Lagi';
                submitButton.dataset.mode = 'run';
            })
            .finally(function () {
                spinner.classList.add('d-none');
                submitButton.disabled = false;
            });
    });
})();
JS);
?>