<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $dwInfo */

$this->title = 'Data Warehouse';
$this->params['breadcrumbs'][] = $this->title;

$tables = $dwInfo['result']['data']['tables'] ?? [];
?>
<div class="datawarehouse-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Table</th>
                            <th>Description</th>
                            <th>Columns</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tables as $name => $meta): ?>
                            <tr>
                                <td><?= Html::a(Html::encode($name), ['datawarehouse/view', 'table' => $name]) ?></td>
                                <td><?= Html::encode($meta['description'] ?? '') ?></td>
                                <td><?= count($meta['columns'] ?? []) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($tables)): ?>
                            <tr><td colspan="3" class="text-muted">No tables available.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
