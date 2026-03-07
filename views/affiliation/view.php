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
                                    // check table existence in information_schema
                                    $safeTable = $mysqli->real_escape_string($table);
                                    $safeCol = $mysqli->real_escape_string($col);
                                    $tblQ = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '" . $mysqli->real_escape_string($sys->database_name) . "' AND table_name = '" . $safeTable . "'";
                                    $tblRes = $mysqli->query($tblQ);
                                    $tblExists = false;
                                    if ($tblRes && ($r = $tblRes->fetch_row())) {
                                        $tblExists = (int)$r[0] > 0;
                                        $tblRes->free();
                                    }

                                    if (!$tblExists) {
                                        $mysqlErrors[] = "Table not found: $table";
                                        continue;
                                    }

                                    // check column existence
                                    $colQ = "SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = '" . $mysqli->real_escape_string($sys->database_name) . "' AND table_name = '" . $safeTable . "' AND column_name = '" . $safeCol . "'";
                                    $colRes = $mysqli->query($colQ);
                                    $colExists = false;
                                    if ($colRes && ($rc = $colRes->fetch_row())) {
                                        $colExists = (int)$rc[0] > 0;
                                        $colRes->free();
                                    }

                                    if (!$colExists) {
                                        $mysqlErrors[] = "Column not found: $col (in $table)";
                                        continue;
                                    }

                                    // run safe count
                                    $sql = "SELECT COUNT(*) AS cnt FROM `" . $safeTable . "` WHERE `" . $safeCol . "` = '" . $code . "'";
                                    $res = $mysqli->query($sql);
                                    if ($res && ($row = $res->fetch_row())) {
                                        $count = (int)$row[0];
                                        $res->free();
                                        break;
                                    }
                                }

                                if ($count === null) {
                                    // try total patients table without filter as fallback if exists
                                    $tblQ = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '" . $mysqli->real_escape_string($sys->database_name) . "' AND table_name = 'patients'";
                                    $tblRes = $mysqli->query($tblQ);
                                    $tblExists = false;
                                    if ($tblRes && ($r = $tblRes->fetch_row())) {
                                        $tblExists = (int)$r[0] > 0;
                                        $tblRes->free();
                                    }
                                    if ($tblExists) {
                                        $fallbackSql = "SELECT COUNT(*) AS cnt FROM `patients`";
                                        $res2 = $mysqli->query($fallbackSql);
                                        if ($res2 && ($row2 = $res2->fetch_row())) {
                                            $count = (int)$row2[0];
                                            $res2->free();
                                        }
                                    } else {
                                        $mysqlErrors[] = 'patients table not found';
                                    }
                                }

                                if ($count === null && !empty($mysqlErrors)) {
                                    $uniq = array_values(array_unique($mysqlErrors));
                                    $err = implode('; ', $uniq);
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
