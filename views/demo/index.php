<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\web\YiiAsset;

/** @var yii\web\View $this */
/** @var app\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Demo Prototype';
$this->params['breadcrumbs'][] = $this->title;

// Register YiiAsset for data-method support
YiiAsset::register($this);

// Get auth manager for role checking
$auth = Yii::$app->authManager;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="ri-user-line me-2"></i><?= Html::encode($this->title) ?>
                </h4>
            </div>


            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Function Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <i class="ri-key-2-line me-2 text-muted"></i>
                                    <?= Html::encode('Clear datawarehouse & operational datawarehouse') ?>
                                </td>
                                <td><?= Html::a('<i class="ri-play-bin-line"></i> Run', ['delete-datawarehouse'], [
                                        'class' => 'btn btn-primary btn-sm',
                                        'data-confirm' => 'Are you sure you want to clear datawarehouse & operational datawarehouse?',
                                        'data-method' => 'post',
                                    ]) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>