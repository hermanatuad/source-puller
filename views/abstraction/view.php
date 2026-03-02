<?php

use app\models\AbstractionColumnSearch;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\Abstraction $model */

$this->title = $model->table_name;
$this->params['breadcrumbs'][] = ['label' => 'Abstractions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="ri-server-line me-2"></i>Abstraction Details
                </h4>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-code-s-slash-line me-2 text-muted"></i>ID
                                    </th>
                                    <td><?= Html::encode($model->id) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-code-s-slash-line me-2 text-muted"></i>Data Name
                                    </th>
                                    <td><?= Html::encode($model->table_name) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-code-s-slash-line me-2 text-muted"></i>Table Warehouse Name
                                    </th>
                                    <td><?= Html::encode($model->table_warehouse) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-code-s-slash-line me-2 text-muted"></i>Type
                                    </th>
                                    <td><?= Html::encode($model->type) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-code-s-slash-line me-2 text-muted"></i>Description
                                    </th>
                                    <td><?= Html::encode($model->description) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="ri-server-line me-2"></i>Abstraction <?= Html::encode($model->table_name) ?>
                </h4>

                <?= Html::button('<i class="ri-add-line align-bottom me-1"></i> Add Column', [
                    'class' => 'btn btn-success btn-sm',
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#modal-bridge',
                    'id' => 'btn-add-bridge',
                ]) ?>

                <div class="modal fade" id="modal-bridge" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalgridLabel">Abstraction Column <?= $model->table_name ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="javascript:void(0);">
                                    <div class="row g-3">
                                        <input type="text" class="form-control" id="table-id" value="">
                                        <input type="text" class="form-control" id="table-name" value="<?= Html::encode($model->id) ?>">

                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="column-type" class="form-label">Column Type</label>
                                                <select class="form-select" id="column-type" aria-label="Default select example">
                                                    <option selected>Select column type</option>
                                                    <option value="string">String</option>
                                                    <option value="number">Number</option>
                                                    <option value="time">Time</option>
                                                </select>

                                            </div>
                                        </div><!--end col-->
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="column-warehouse" class="form-label">Column Warehouse</label>
                                                <input type="text" class="form-control" id="column-warehouse" placeholder="Enter column warehouse">
                                            </div>
                                        </div><!--end col-->
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="description" class="form-label">Description</label>
                                                <textarea class="form-control" rows="2" id="description" placeholder="Enter description"></textarea>
                                            </div>
                                        </div><!--end col-->
                                        <div class="col-lg-12">
                                            <div class="hstack gap-2 justify-content-end">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary" id="submit-column">Submit</button>
                                            </div>
                                        </div><!--end col-->
                                    </div><!--end row-->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">

                    <?php
                    $searchModel = new AbstractionColumnSearch();
                    $dataProvider = $searchModel->search(array_merge(Yii::$app->request->queryParams, ['AbstractionColumnSearch' => ['abstraction_id' => $model->id]]));
                    ?>

                    <div class="table-responsive">
                        <?php Pjax::begin(['id' => 'bridges-pjax']); ?>
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            // 'filterModel' => $searchModel,
                            'summary' => false,
                            'tableOptions' => ['class' => 'table table-hover align-middle mb-0'],
                            'columns' => [
                                ['class' => 'yii\\grid\\SerialColumn'],
                                'abstraction_id',
                                'column_type',
                                'column_warehouse',
                                'description',
                                ['class' => 'yii\\grid\\ActionColumn', 'controller' => 'bridge'],
                            ],
                        ]) ?>
                        <?php Pjax::end(); ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php \richardfan\widget\JSRegister::begin(); ?>
<script>
    $('#submit-column').on('click', function(e) {
        e.preventDefault();
        console.log('submit column');
    })
</script>
<?php \richardfan\widget\JSRegister::end(); ?>