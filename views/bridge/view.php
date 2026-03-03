<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Bridge $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Bridges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="bridge-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'system_code',
            'bridge_type',
            'bridge_source',
            'bridge_target',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>


<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="ri-server-line me-2"></i> Brige Column
                </h4>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        Requirements
                    </div>
                    <div class="col-md-6">
                        Sources
                    </div>

                    <div class="col-md-12">

                        <table class="table table-borderless mb-0">
                            <tbody>
                                <?php foreach ($abstractionColumn as $key) : ?>
                                    <tr>
                                        <th scope="row" style="width: 200px;">
                                            <i class="ri-hashtag me-2 text-muted"></i><?= $key->column_warehouse ?>
                                        </th>
                                        <td><input type="text" name="" id="" class="form-control"></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>