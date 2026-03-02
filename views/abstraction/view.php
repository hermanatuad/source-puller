<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Abstraction $model */

$this->title = $model->id;
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
                    <div class="col-md-6">
                        <table class="table table-borderless mb-0">
                            <tbody>
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
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <table class="table table-borderless mb-0">
                            <tbody>
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
                    <i class="ri-server-line me-2"></i>Data Sources
                </h4>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        Affiliation
                    </div>
                    <div class="col-md-6">
                        Sources
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>