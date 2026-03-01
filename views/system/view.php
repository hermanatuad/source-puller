<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
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
                    <?=  Html::a('<i class="ri-refresh-line align-bottom me-1"></i> Clear Cache', ['clear-cache', 'id' => $model->id], [
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
                <?= Html::button('<i class="ri-add-line align-bottom me-1"></i> Add Bridge', [
                    'class' => 'btn btn-success btn-sm',
                    'id' => 'btn-add-bridge',
                    'data-url' => Url::to(['bridge/create', 'system_code' => $model->system_code]),
                ]) ?>
            </div>
            <div class="card-body p-0">
                <?php
                $searchModel = new BridgeSearch();
                $dataProvider = $searchModel->search(array_merge(Yii::$app->request->queryParams, ['BridgeSearch' => ['system_code' => $model->system_code]]));
                ?>

                <div class="table-responsive">
                <?php Pjax::begin(['id' => 'bridges-pjax']); ?>
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
                <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Add Bridge -->
<div class="modal fade" id="modal-bridge" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Bridge</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modal-bridge-body">
        <div class="text-center py-4"><div class="spinner-border" role="status"></div></div>
      </div>
    </div>
  </div>
</div>

<?php
$this->registerJs(<<<'JS'
;(function($){
    var btn = $('#btn-add-bridge');
    var modal = $('#modal-bridge');
    var modalBody = $('#modal-bridge-body');

    btn.off('click').on('click', function(e){
        e.preventDefault();
        var url = btn.data('url');
        if (!url) return;

        modalBody.html('<div class="text-center py-4"><div class="spinner-border" role="status"></div></div>');
        var bsModal = new bootstrap.Modal(modal[0]);
        bsModal.show();

        $.get(url, function(html){
            modalBody.html(html);
        }).fail(function(){
            modalBody.html('<div class="alert alert-danger">Failed to load form.</div>');
        });
    });

    // Delegate submit inside modal (handles dynamic content)
    modal.on('submit', 'form', function(e){
        e.preventDefault();
        var form = $(this);
        var action = form.attr('action') || form.data('url');
        var data = form.serialize();

        $.post(action, data, function(res){
            if (res && res.status === 'success') {
                // close modal and reload bridges grid
                var bsModal = bootstrap.Modal.getInstance(modal[0]);
                bsModal.hide();
                $.pjax.reload({container:'#bridges-pjax'});
            } else {
                // Replace modal body with returned HTML (validation errors)
                if (typeof res === 'string') {
                    modalBody.html(res);
                } else if (res && res.message) {
                    modalBody.html('<div class="alert alert-danger">'+(res.message||'Error')+'</div>');
                }
            }
        }, 'json').fail(function(xhr){
            // If server returned HTML (non-json), replace modal body
            if (xhr.responseText) {
                modalBody.html(xhr.responseText);
            } else {
                modalBody.html('<div class="alert alert-danger">Request failed.</div>');
            }
        });
    });

})(jQuery);
JS
);
?>