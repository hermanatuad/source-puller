<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var string $tableName */
/** @var array|null $tableData */
/** @var array $dwInfo */

$this->title = 'Table: ' . $tableName;
$this->params['breadcrumbs'][] = ['label' => 'Data Warehouse', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$columns = $tableData['columns'] ?? [];
?>
<div class="datawarehouse-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Back to list', ['datawarehouse/index'], ['class' => 'btn btn-secondary']) ?>
    </p>

    <div class="card">
        <div class="card-body">
            <h5>Description</h5>
            <p class="text-muted"><?= Html::encode($tableData['description'] ?? '') ?></p>

            <h5>Columns</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($columns)): ?>
                            <?php foreach ($columns as $col): ?>
                                <tr>
                                    <td><?= Html::encode($col['name'] ?? '') ?></td>
                                    <td><?= Html::encode($col['type'] ?? '') ?></td>
                                    <td><?= Html::encode($col['comment'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-muted">No columns available.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
