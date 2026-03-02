<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Entity $model */

$this->title = $model->entity_id;
$this->params['breadcrumbs'][] = ['label' => 'Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="ri-server-line me-2"></i>Entity Details
                </h4>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center border-end">
                            <div class="avatar-xl mx-auto mb-3">
                                <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-1">
                                    <?= Html::encode(strtoupper(substr($model->entity_id, 0, 2))) ?>
                                </div>
                            </div>
                            <h5 class="mb-1"><?= Html::encode($model->entity_id) ?></h5>
                            <p class="text-muted mb-2"><small><?= Html::encode($model->id) ?></small></p>
                            <span class="badge bg-info-subtle text-info mb-3">
                                <?= Html::encode($model->is_alive) ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-code-s-slash-line me-2 text-muted"></i>Entity ID
                                    </th>
                                    <td><?= Html::encode($model->entity_id) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-code-s-slash-line me-2 text-muted"></i>Life Status
                                    </th>
                                    <td><?= Html::encode($model->is_alive) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-code-s-slash-line me-2 text-muted"></i>Last Data Update
                                    </th>
                                    <td><?= Yii::$app->formatter->asDatetime($model->updated_at) ?></td>
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