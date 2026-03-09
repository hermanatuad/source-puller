<?php

use app\helpers\DBHelper;
use app\helpers\DWHelper;
use app\helpers\MyHelper;
use app\models\BridgeColumn;
use app\models\System;
use yii\helpers\Html;
use app\assets\KonvaAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Bridge $model */

$system = System::find()->where(['system_code' => $model->system_code])->one();
$this->title = $model->bridge_name;
$this->params['breadcrumbs'][] = ['label' => 'Systems', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $system->system_name, 'url' => ['system/view', 'id' => $system->id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
KonvaAsset::register($this);
?>
<div class="bridge-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="text-end mb-3">
        <?= Html::a('<i class="ri-arrow-left-line"></i> Back', ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="ri-delete-bin-2-line"></i> Delete', ['delete', 'id' => $model->id], [
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
            'bridge_table_source',
            'bridge_table_target',
            [
                'attribute' => 'bridge_type',
                'value' => function ($model) {
                    return ucfirst($model->bridge_type);
                }
            ],
            'created_at:datetime',
        ],
    ]) ?>

</div>

<!-- Konva schema visualization for source DB -->
<?php
$dbInfoAll = DBHelper::getDatabaseInfoFromCache($system);
$allTables = array_keys($dbInfoAll['result']['tables'] ?? []);
$sourceTable = $model->bridge_table_source ?? null;
// prepare schema payload for Konva
$schemaPayload = [];
foreach (($dbInfoAll['result']['tables'] ?? []) as $tname => $t) {
    $cols = [];
    if (!empty($t['columns']) && is_array($t['columns'])) {
        foreach ($t['columns'] as $c) {
            if (is_array($c)) {
                $cols[] = [
                    'name' => $c['name'] ?? '',
                    'type' => $c['data_type'] ?? ($c['column_type'] ?? ''),
                    'nullable' => !empty($c['nullable']),
                    'key' => $c['key'] ?? '',
                    'extra' => $c['extra'] ?? '',
                ];
            } else {
                $cols[] = ['name' => (string)$c, 'type' => '', 'nullable' => false, 'key' => '', 'extra' => ''];
            }
        }
    }
    $schemaPayload[] = ['name' => $tname, 'columns' => $cols, 'foreign_keys' => $t['foreign_keys'] ?? []];
}
$schemaJson = json_encode($schemaPayload, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?>

<div class="row">
    <div class="col-lg-12">
        <div class="card mb-3">
            <div class="card-header"><strong>Schema Diagram (visual)</strong></div>
            <div class="card-body">
                <div id="bridge-schema-canvas" style="width:100%; height:420px; border:1px solid #eee;"></div>
            </div>
        </div>
    </div>
</div>

<?php \richardfan\widget\JSRegister::begin(); ?>
<script>
    (function() {
        var schema = <?= $schemaJson ?> || [];
        var sourceTable = <?= json_encode($sourceTable) ?> || '';
        var container = document.getElementById('bridge-schema-canvas');
        if (!container) return;

        var width = Math.max(container.clientWidth, 900);
        var height = Math.max(container.clientHeight, 420);

        var stage = new Konva.Stage({
            container: 'bridge-schema-canvas',
            width: width,
            height: height
        });
        var layer = new Konva.Layer();
        stage.add(layer);
        // separate layer for arrows so they can be drawn above tables
        var arrowsLayer = new Konva.Layer();
        stage.add(arrowsLayer);

        var paddingX = 20,
            paddingY = 20,
            boxWidth = 220,
            boxHeightBase = 24,
            lineHeight = 18,
            headerHeight = 28;
        var colsPerRow = Math.max(1, Math.ceil(Math.sqrt(schema.length)));

        var groups = {};

        // first pass: create groups
        schema.forEach(function(tbl, idx) {
            var row = Math.floor(idx / colsPerRow);
            var col = idx % colsPerRow;
            var x = paddingX + col * (boxWidth + paddingX);
            var cols = tbl.columns || [];
            var boxH = headerHeight + Math.max(1, cols.length) * lineHeight + 12;

            var isSource = (tbl.name === sourceTable);
            var w = isSource ? boxWidth * 1.3 : boxWidth;
            var h = isSource ? boxH * 1.15 : boxH;

            var group = new Konva.Group({
                x: x,
                y: paddingY + row * (h + paddingY),
                draggable: true
            });

            // container background with shadow
            var containerRect = new Konva.Rect({
                x: 0,
                y: 0,
                width: w,
                height: h,
                fill: '#ffffff',
                cornerRadius: 6,
                shadowColor: '#000',
                shadowBlur: 6,
                shadowOffset: { x: 2, y: 2 },
                shadowOpacity: 0.08,
            });

            // header
            var headerRect = new Konva.Rect({
                x: 0,
                y: 0,
                width: w,
                height: headerHeight,
                fill: isSource ? '#0b5e3b' : '#0d6efd',
                cornerRadius: 6
            });
            var headerText = new Konva.Text({
                x: 10,
                y: Math.max(2, (headerHeight - (isSource ? 16 : 14)) / 2),
                text: tbl.name || '(table)',
                fontSize: isSource ? 14 : 13,
                fontStyle: 'bold',
                fontFamily: 'Courier New, monospace',
                fill: '#fff'
            });

            // separator line
            var sep = new Konva.Rect({ x: 0, y: headerHeight - 1, width: w, height: 1, fill: '#e9ecef' });

            group.add(containerRect);
            group.add(headerRect);
            group.add(sep);
            group.add(headerText);

            // add row backgrounds and texts
            (cols || []).forEach(function(col, i) {
                var y = headerHeight + 6 + i * lineHeight;
                var rowBg = new Konva.Rect({ x: 0, y: y - 4, width: w, height: lineHeight + 6, fill: (i % 2 === 0) ? '#ffffff' : '#fbfbfb' });
                var text = (col.key && col.key.toUpperCase() === 'PRI' ? 'PK ' : '') + col.name + (col.type ? ' : ' + col.type : '') + (col.nullable ? '' : ' (NOT NULL)');
                var txt = new Konva.Text({
                    x: 10,
                    y: y,
                    text: text,
                    fontSize: 12,
                    fontFamily: 'Courier New, monospace',
                    fill: col.key && col.key.toUpperCase() === 'PRI' ? '#c7254e' : '#333'
                });
                group.add(rowBg);
                group.add(txt);
            });

            // visual stroke for highlighted source
            if (isSource) {
                var border = new Konva.Rect({ x: 0, y: 0, width: w, height: h, stroke: '#0f5132', strokeWidth: 2, cornerRadius: 6 });
                group.add(border);
            }

            layer.add(group);
            groups[tbl.name] = {
                group: group,
                w: w,
                h: h
            };
        });

        // second pass: draw simple links for foreign keys if present
        var links = [];
        schema.forEach(function(tbl) {
            var fks = tbl.foreign_keys || [];
            if (!Array.isArray(fks)) return;
            fks.forEach(function(fk) {
                // try common properties for referenced table
                var refTable = fk.referenced_table || fk.reference_table || fk.foreign_table || fk.table || null;
                if (!refTable) return;
                var src = groups[tbl.name];
                var dst = groups[refTable];
                if (!src || !dst) return;

                var sx = src.group.x() + src.w - 6;
                var sy = src.group.y() + headerHeight + 10;
                var dx = dst.group.x() + 6;
                var dy = dst.group.y() + headerHeight + 10;

                var arrow = new Konva.Arrow({
                    points: [sx, sy, dx, dy],
                    pointerLength: 8,
                    pointerWidth: 8,
                    fill: '#666',
                    stroke: '#666',
                    strokeWidth: 1
                });
                layer.add(arrow);
                links.push({ arrow: arrow, srcName: tbl.name, dstName: refTable });
            });
        });

        // update arrow points when groups move
        function updateArrows() {
            links.forEach(function(l) {
                var src = groups[l.srcName];
                var dst = groups[l.dstName];
                if (!src || !dst) return;
                var sx = src.group.x() + src.w - 6;
                var sy = src.group.y() + headerHeight + 10;
                var dx = dst.group.x() + 6;
                var dy = dst.group.y() + headerHeight + 10;
                l.arrow.points([sx, sy, dx, dy]);
            });
            layer.batchDraw();
        }

        // attach drag listeners for each group
        Object.keys(groups).forEach(function(name) {
            var g = groups[name].group;
            g.on('dragmove', updateArrows);
            g.on('dragend', updateArrows);
        });

        // initial draw and ensure arrows positioned correctly
        updateArrows();
        layer.draw();
    })();
</script>
<?php \richardfan\widget\JSRegister::end(); ?>

<div class="row">

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="ri-server-line me-2"></i> Extraction Pipeline Columns
                </h4>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Column Sources</th>
                                <th>Column Warehouse</th>
                                <th>Column Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $system = System::find()->where(['system_code' => $model->system_code])->one();
                            $dbInfo = DBHelper::getDatabaseInfoFromCache($system);
                            $columns = $dbInfo['result']['tables'][$model->bridge_table_source]['columns'] ?? [];
                            if (!empty($columns)):
                            ?>
                                <?php foreach ($columns as $column): ?>
                                    <?php
                                    $colName = $column['name'] ?? null;
                                    // bridgeColumnList is target => source, so find target for this source
                                    $targetCol = $colName ? array_search($colName, $bridgeColumnList, true) : false;
                                    $isLinked = $targetCol !== false && $targetCol !== null;
                                    $rowClass = $isLinked ? 'table-success' : 'table-warning';
                                    ?>
                                    <tr class="<?= $rowClass ?>">
                                        <td><?= Html::encode($colName ?: 'N/A') ?></td>
                                        <td><?= Html::encode($targetCol ?: 'N/A') ?></td>
                                        <td><?= MyHelper::ColumnTypeList()[$bridgeColumnTypeList[$colName] ?? ''] ?? 'N/A' ?></td>
                                        <td>
                                            <?= Html::a('<i class="ri-edit-2-line"></i>', ['bridge-column/update', 'bridge_id' => $model->id, 'source_column_name' => $colName], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                                            <?= Html::a('<i class="ri-delete-bin-2-line"></i>', ['bridge-column/delete', 'bridge_id' => $model->id, 'source_column_name' => $colName], [
                                                'class' => 'btn btn-sm btn-outline-danger',
                                                'data' => [
                                                    'confirm' => 'Are you sure you want to delete this item?',
                                                    'method' => 'post',
                                                ],
                                            ]) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted">No permissions assigned</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="ri-server-line me-2"></i> Column Warehouse
                </h4>
            </div>
            <div class="card-body">
                <?php
                $DWInfo = DWHelper::getDWInfoFromCache();
                $targetColumns = $DWInfo['result']['data']['tables'][$model->bridge_table_target]['columns'] ?? [];
                ?>

                <div class="table-responsive">
                    <table class="table table-hover align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Warehouse Column</th>
                                <th>Type</th>
                                <th>Mapped Source</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($targetColumns)): ?>
                                <?php foreach ($targetColumns as $tcol):
                                    $tName = $tcol['name'] ?? null;
                                    $mappedSource = $tName ? ($bridgeColumnList[$tName] ?? null) : null;
                                    $rowClass = $mappedSource ? 'table-success' : 'table-danger';
                                ?>
                                    <tr class="<?= $rowClass ?>">
                                        <td><?= Html::encode($tName ?: '-') ?></td>
                                        <td><?= Html::encode($tcol['data_type'] ?? $tcol['column_type'] ?? '-') ?></td>
                                        <td>
                                            <?php if ($mappedSource): ?>
                                                <span class="badge bg-success">Linked: <?= Html::encode($mappedSource) ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Unlinked</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No warehouse columns available or unable to fetch schema</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>