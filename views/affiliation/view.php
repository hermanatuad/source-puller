<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\Affiliation $model */

$this->title = $model->affiliation_name;
$this->params['breadcrumbs'][] = ['label' => 'Affiliations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="affiliation-view">

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
            'affiliation_code',
            'affiliation_name',
            'address',
        ],
    ]) ?>

</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Data Warehouse</h5>
                <?php
                $dwCount = null;
                $dwError = null;
                if (Yii::$app->has('dbDataWarehouse')) {
                    try {
                        $db = Yii::$app->dbDataWarehouse;
                        $code = $model->affiliation_code;
                        // Try several common table/column combinations
                        $tries = [
                            ['patients', 'affiliation_code'],
                            ['patients', 'affiliation_id'],
                            ['patient', 'affiliation_code'],
                            ['patient', 'affiliation_id'],
                            ['dim_patient', 'affiliation_code'],
                            ['dim_patient', 'affiliation_id'],
                        ];
                        foreach ($tries as $t) {
                            list($table, $col) = $t;
                            $sql = "SELECT COUNT(*) FROM \"$table\" WHERE \"$col\" = :code";
                            try {
                                $count = $db->createCommand($sql, [':code' => $code])->queryScalar();
                                if ($count !== false) {
                                    $dwCount = (int)$count;
                                    break;
                                }
                            } catch (\Exception $e) {
                                // ignore and try next
                            }
                        }
                        if ($dwCount === null) {
                            // As a last resort try a COUNT(*) on a 'patients' table without filter
                            try {
                                $count = $db->createCommand('SELECT COUNT(*) FROM "patients"')->queryScalar();
                                if ($count !== false) {
                                    $dwCount = (int)$count;
                                }
                            } catch (\Exception $e) {
                                $dwError = $e->getMessage();
                            }
                        }
                    } catch (\Exception $e) {
                        $dwError = $e->getMessage();
                    }
                } else {
                    $dwError = 'dbDataWarehouse not configured';
                }
                ?>

                <?php if ($dwCount !== null): ?>
                    <p class="h2"><?= Html::encode($dwCount) ?></p>
                    <p class="text-muted">Patients (matching affiliation when available)</p>
                <?php else: ?>
                    <p class="text-danger"><?php echo Html::encode($dwError ?: 'No data available'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <h4>Users for this affiliation</h4>
        <?php if (!empty($users)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                        <tbody>
                            <?php foreach ($users as $index => $user): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= Html::encode($user->username) ?></td>
                                    <td><?= Html::encode($user->email) ?></td>
                                    <td>
                                        <?= Html::a('View', ['user/view', 'id' => $user->   id], ['class' => 'btn btn-sm btn-info']) ?>
                                        <?= Html::a('Edit', ['user/update', 'id' => $user->id], ['class' => 'btn btn-sm btn-primary']) ?>
                                        <?= Html::a('Delete', ['user/delete', 'id' => $user->id], [
                                            'class' => 'btn btn-sm btn-danger',
                                            'data' => [
                                                'confirm' => 'Are you sure you want to delete this user?',
                                                'method' => 'post',
                                            ],
                                        ]) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">No users associated with this affiliation.</p>
        <?php endif; ?>
    </div>
</div>
</div>
