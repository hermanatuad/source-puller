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
        <?php
        $dataProvider = new ActiveDataProvider([
            'query' => \app\models\User::find()->where(['affiliation_code' => $model->affiliation_code]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        Pjax::begin(['id' => 'affiliation-users-pjax']);
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'id',
                'username',
                'name',
                'email',
                'access_role',
                [
                    'attribute' => 'status',
                    'value' => function($m) { return $m->status; }
                ],
                [
                    'attribute' => 'created_at',
                    'format' => ['datetime', 'php:Y-m-d H:i'],
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'controller' => 'user',
                ],
            ],
        ]);
        Pjax::end();
        ?>
    </div>
</div>
</div>
