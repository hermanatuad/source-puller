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
                            ['patient', 'affiliation_code'],
                            ['patient', 'affiliation_id'],
                            ['patient', 'affiliation_code'],
                            ['patient', 'affiliation_id'],
                            ['dim_patient', 'affiliation_code'],
                            ['dim_patient', 'affiliation_id'],
                        ];
                        $dwErrors = [];
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
                                $msg = $e->getMessage();
                                if (stripos($msg, 'relation') !== false || stripos($msg, 'does not exist') !== false) {
                                    $dwErrors[] = "Table not found: $table";
                                } elseif (stripos($msg, 'column') !== false) {
                                    $dwErrors[] = "Column not found: $col";
                                } else {
                                    $dwErrors[] = $msg;
                                }
                                // try next combo
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
                                $dwErrors[] = 'patients table not found';
                            }
                        }

                        if ($dwCount === null) {
                            $dwError = !empty($dwErrors) ? implode('; ', array_values(array_unique($dwErrors))) : 'No matching patients table/column found in DW';
                        }
                    } catch (\Exception $e) {
                        $dwError = 'DW query error: ' . $e->getMessage();
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
    <div class="col-md-12 mt-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Source Systems Patient Counts</h5>
                <?php
                $systems = \app\models\System::find()->where(['affiliation_code' => $model->affiliation_code])->all();
                if (!empty($systems)) {
                    ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>System</th>
                                    <th>DB Type</th>
                                    <th>Database</th>
                                    <th>Patient Count</th>
                                </tr>
                            </thead>
                            <tbody>
                    <?php
                    foreach ($systems as $sys) {
                        $count = null;
                        $err = null;
                        try {
                            if ($sys->system_type === 'mysql') {
                                $mysqli = new \mysqli($sys->hostname, $sys->username, $sys->password, $sys->database_name, $sys->port ?: 3306);
                                if ($mysqli->connect_error) {
                                    throw new \Exception('Connection failed: ' . $mysqli->connect_error);
                                }

                                $code = $mysqli->real_escape_string($model->affiliation_code);
                                $tries = [
                                    ['patients', 'affiliation_code'],
                                    ['patients', 'affiliation_id'],
                                    ['patient', 'affiliation_code'],
                                    ['patient', 'affiliation_id'],
                                ];

                                $mysqlErrors = [];
                                foreach ($tries as $t) {
                                    list($table, $col) = $t;
                                    $safeTable = $mysqli->real_escape_string($table);
                                    $safeCol = $mysqli->real_escape_string($col);
                                    $sql = "SELECT COUNT(*) AS cnt FROM `" . $safeTable . "` WHERE `" . $safeCol . "` = '" . $code . "'";
                                    $res = $mysqli->query($sql);
                                    if ($res && ($row = $res->fetch_row())) {
                                        $count = (int)$row[0];
                                        $res->free();
                                        break;
                                    }
                                    if ($mysqli->errno) {
                                        $mysqlErrors[] = $mysqli->error;
                                        // reset error for next attempt
                                        // no mysqli->clear_errors in older PHP, ignore
                                    }
                                }

                                if ($count === null) {
                                    // try total patients table without filter as fallback
                                    $fallbackSql = "SELECT COUNT(*) AS cnt FROM `patients`";
                                    $res2 = $mysqli->query($fallbackSql);
                                    if ($res2 && ($row2 = $res2->fetch_row())) {
                                        $count = (int)$row2[0];
                                        $res2->free();
                                    } else {
                                        if ($mysqli->errno) {
                                            $mysqlErrors[] = $mysqli->error;
                                        }
                                    }
                                }

                                if ($count === null && !empty($mysqlErrors)) {
                                    // prefer short, friendly messages
                                    $uniq = array_values(array_unique($mysqlErrors));
                                    $err = implode('; ', array_map(function($m){
                                        if (stripos($m, 'Unknown column') !== false) return 'Column not found';
                                        if (stripos($m, 'doesn\'t exist') !== false) return 'Table not found';
                                        return $m;
                                    }, $uniq));
                                }

                                $mysqli->close();
                            } else {
                                $err = 'Unsupported DB type';
                            }
                        } catch (\Throwable $e) {
                            $err = $e->getMessage();
                        }

                        echo '<tr>';
                        echo '<td>' . Html::encode($sys->system_code) . '</td>';
                        echo '<td>' . Html::encode($sys->system_type) . '</td>';
                        echo '<td>' . Html::encode($sys->database_name) . '</td>';
                        if ($err !== null) {
                            echo '<td class="text-danger">' . Html::encode($err) . '</td>';
                        } else {
                            echo '<td>' . Html::encode($count === null ? '-' : $count) . '</td>';
                        }
                        echo '</tr>';
                    }
                    ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                } else {
                    echo '<p class="text-muted">No source systems registered for this affiliation.</p>';
                }
                ?>
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
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                        <tbody>
                            <?php foreach ($users as $index => $user): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= Html::encode($user->username) ?></td>
                                    <td><?= Html::encode($user->email) ?></td>
                                    <td><?= Html::encode($user->name) ?></td>
                                    <td>
                                        <?= Html::a('View', ['user/view', 'id' => $user->id], ['class' => 'btn btn-sm btn-info']) ?>
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
