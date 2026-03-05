<?php

use app\models\Entity;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var app\models\EntitySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Datawarehouse';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="ri-database-2-line me-2"></i><?= Html::encode($this->title) ?>
                </h4>
                <div class="text-muted small">
                    <?php if (!empty($dwInfo['cache_info']['cached_at'])): ?>
                        Cached: <?= Html::encode($dwInfo['cache_info']['cached_at']) ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-body">
                <?php
                $tables = $dwInfo['result']['data']['tables'] ?? [];
                if (empty($tables)) {
                    echo '<div class="alert alert-info">No datawarehouse tables available in cache.</div>';
                } else {
                    $accordionId = 'dwAccordion';
                    ?>
                    <div class="accordion" id="<?= $accordionId ?>">
                        <?php foreach ($tables as $tableName => $meta) {
                            $cols = $meta['columns'] ?? [];
                            $safeId = 't_' . md5($tableName);
                            ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-<?= $safeId ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $safeId ?>" aria-expanded="false" aria-controls="collapse-<?= $safeId ?>">
                                        <div class="me-3">
                                            <strong><?= Html::encode($tableName) ?></strong>
                                        </div>
                                        <div class="text-muted small ms-2">
                                            <?= Html::encode($meta['columns_count'] ?? count($cols)) ?> col • <?= Html::encode($meta['total_size_mb'] ?? '') ?> MB
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse-<?= $safeId ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?= $safeId ?>" data-bs-parent="#<?= $accordionId ?>">
                                    <div class="accordion-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <div class="small text-muted">Columns: <?= Html::encode(count($cols)) ?></div>
                                            </div>
                                            <div>
                                                <?= Html::a('View Schema', ['datawarehouse/view', 'table' => $tableName], ['class' => 'btn btn-sm btn-outline-primary me-2']) ?>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Type</th>
                                                    <th>Nullable</th>
                                                    <th>Default</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach ($cols as $c): ?>
                                                    <tr>
                                                        <td><?= Html::encode($c['name'] ?? '') ?></td>
                                                        <td><?= Html::encode($c['data_type'] ?? '') ?></td>
                                                        <td><?= (!empty($c['nullable']) ? 'YES' : 'NO') ?></td>
                                                        <td><?= Html::encode($c['default'] ?? '') ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>