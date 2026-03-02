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
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="ri-server-line me-2"></i>Abstraction <?= Html::encode($model->table_name) ?>
                </h4>
            </div>

            <div class="card-body">
                <div class="row">

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

                    <div class="card mt-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>Bridges for this System</div>

                            <?= Html::button('<i class="ri-add-line align-bottom me-1"></i> Add Bridge', [
                                'class' => 'btn btn-success btn-sm',
                                'data-bs-toggle' => 'modal',
                                'data-bs-target' => '#modal-bridge',
                                'id' => 'btn-add-bridge',
                            ]) ?>
                        </div>
                        <div class="card-body p-0">
                            <?php
                            $searchModel = new AbstractionColumnSearch();
                            $dataProvider = $searchModel->search(array_merge(Yii::$app->request->queryParams, ['AbstractionColumnSearch' => ['abstraction_id' => $model->id]]));
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
                                        'abstraction_id',
                                        'column_type',
                                        'column_warehouse',
                                        'description',
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
        </div>

    </div>
</div>