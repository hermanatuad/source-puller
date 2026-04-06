<?php

use app\helpers\DBHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\BridgeSearch;
use richardfan\widget\JSRegister;
use app\assets\KonvaAsset;

/** @var yii\web\View $this */
/** @var app\models\System $model */

$this->title = $model->system_name ?: $model->system_code;
$this->params['breadcrumbs'][] = ['label' => 'Source DBS', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
KonvaAsset::register($this);

?>

<div id="container"></div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="ri-server-line me-2"></i>System Details
                </h4>
                <div class="flex-shrink-0">
                    <?= Html::a('<i class="ri-pencil-line align-bottom me-1"></i> Update', ['update', 'id' => $model->id], [
                        'class' => 'btn btn-primary btn-sm me-2'
                    ]) ?>
                    <?= Html::beginForm(['delete', 'id' => $model->id], 'post', ['style' => 'display:inline']) ?>
                    <button type="submit" class="btn btn-danger btn-sm me-2" onclick="return confirm('Are you sure you want to delete this system?')">
                        <i class="ri-delete-bin-line align-bottom me-1"></i> Delete
                    </button>
                    <?= Html::endForm() ?>
                    <?= Html::a('<i class="ri-file-text-line align-bottom me-1"></i> Test Connection', ['check-connection', 'id' => $model->id], [
                        'class' => 'btn btn-info btn-sm me-2'
                    ]) ?>
                    <?= Html::a('<i class="ri-refresh-line align-bottom me-1"></i> Clear Cache', ['clear-cache', 'id' => $model->id], [
                        'class' => 'btn btn-secondary btn-sm me-2'
                    ]) ?>
                    <!-- <button type="button" class="btn btn-warning btn-sm me-2 btn-show-cache" data-url="<?= Html::encode(Url::to(['system/cache-data', 'id' => $model->id])) ?>">
                        <i class="ri-database-2-line align-bottom me-1"></i> View Cache Data
                    </button> -->

                    <button type="button" class="btn btn-warning btn-sm me-2 btn-show-cache" data-url="<?= Html::encode(Url::to(['system/raw-cache', 'id' => $model->id])) ?>">
                        <i class="ri-database-2-line align-bottom me-1"></i> View Raw Cache
                    </button>
                    <?= Html::a('<i class="ri-arrow-left-line align-bottom me-1"></i> Back', ['index'], [
                        'class' => 'btn btn-secondary btn-sm'
                    ]) ?>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center border-end">
                            <div class="avatar-xl mx-auto mb-3">
                                <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-1">
                                    <?= Html::encode(strtoupper(substr($model->system_code, 0, 2))) ?>
                                </div>
                            </div>
                            <h5 class="mb-1"><?= Html::encode($model->system_name) ?></h5>
                            <p class="text-muted mb-2"><?= Html::encode($model->system_code) ?></p>
                            <span class="badge bg-info-subtle text-info mb-3">
                                <?= Html::encode($model->system_type) ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width: 200px;">
                                        <i class="ri-hashtag me-2 text-muted"></i>ID
                                    </th>
                                    <td><?= Html::encode($model->id) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-code-s-slash-line me-2 text-muted"></i>System Code
                                    </th>
                                    <td><?= Html::encode($model->system_code) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-code-s-slash-line me-2 text-muted"></i>Database Name
                                    </th>
                                    <td><?= Html::encode($model->database_name) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-key-2-line me-2 text-muted"></i>Hostname
                                    </th>
                                    <td><?= Html::encode($model->hostname) ?: '<span class="text-muted">-</span>' ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-user-line me-2 text-muted"></i>Username
                                    </th>
                                    <td><?= Html::encode($model->username) ?: '<span class="text-muted">-</span>' ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-lock-line me-2 text-muted"></i>Password
                                    </th>
                                    <td><?= !empty($model->password) ? '<span class="text-muted">••••••</span>' : '<span class="text-muted">-</span>' ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-voiceprint-line me-2 text-muted"></i>Port
                                    </th>
                                    <td><?= Html::encode($model->port) ?: '<span class="text-muted">-</span>' ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-folder-line me-2 text-muted"></i>Path
                                    </th>
                                    <td><?= Html::encode($model->path) ?: '<span class="text-muted">-</span>' ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <i class="ri-file-text-line me-2 text-muted"></i>Description
                                    </th>
                                    <td><?= Html::encode($model->description) ?: '<span class="text-muted">-</span>' ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-bridge" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalgridLabel">Grid Modals</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="javascript:void(0);">
                            <div class="row g-3">
                                <div class="col-xxl-6">
                                    <div>
                                        <label for="firstName" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="firstName" placeholder="Enter firstname">
                                    </div>
                                </div><!--end col-->
                                <div class="col-xxl-6">
                                    <div>
                                        <label for="lastName" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lastName" placeholder="Enter lastname">
                                    </div>
                                </div><!--end col-->
                                <div class="col-lg-12">
                                    <label for="genderInput" class="form-label">Gender</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">
                                            <label class="form-check-label" for="inlineRadio1">Male</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                            <label class="form-check-label" for="inlineRadio2">Female</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3">
                                            <label class="form-check-label" for="inlineRadio3">Others</label>
                                        </div>
                                    </div>
                                </div><!--end col-->
                                <div class="col-xxl-6">
                                    <div>
                                        <label for="emailInput" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="emailInput" placeholder="Enter your email">
                                    </div>
                                </div><!--end col-->
                                <div class="col-xxl-6">
                                    <div>
                                        <label for="passwordInput" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="passwordInput" value="451326546">
                                    </div>
                                </div><!--end col-->
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div><!--end col-->
                            </div><!--end row-->
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-cache-data" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cache Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="modal-cache-message" class="mb-2 text-muted">Loading...</div>
                        <pre id="modal-cache-json" style="max-height:70vh; overflow:auto; white-space:pre-wrap; word-break:break-word;"></pre>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <i class="ri-server-line me-2"></i>Database Schema (ER Diagram)
            </div>
            <div class="card-body">
                <div id="schema-canvas-container" style="width:100%; height:380px; border:1px solid #eee;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="ri-server-line me-2"></i>Data Sources
                </h4>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Table Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // $params = [
                            //     'system_code' => $model->system_code,
                            //     'hostname' => $model->hostname,
                            //     'username' => $model->username,
                            //     'password' => $model->password,
                            //     'port' => $model->port,
                            //     'database_name' => $model->database_name,
                            //     'refresh_on_miss' => false,
                            // ];

                            $dataInfo = DBHelper::getDatabaseInfoFromCache($model);
                            $tables = $dataInfo['result']['tables'] ?? [];
                            $status = $dataInfo['status'] ?? null;
                            $message = $dataInfo['message'] ?? ($dataInfo['result']['message'] ?? null);

                            // Normalize and prepare schema payload with column details
                            if (is_string($tables)) {
                                $decoded = json_decode($tables, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    $tables = $decoded;
                                } else {
                                    $tables = [];
                                }
                            } elseif (!is_array($tables)) {
                                $tables = [];
                            }

                            // SQL Server cache may return associative map: {table_name: {...meta...}}
                            if (!empty($tables) && array_keys($tables) !== range(0, count($tables) - 1)) {
                                $normalizedTables = [];
                                foreach ($tables as $tableName => $tableMeta) {
                                    if (is_array($tableMeta)) {
                                        $tableMeta['name'] = $tableMeta['name'] ?? (string)$tableName;
                                        $normalizedTables[] = $tableMeta;
                                    }
                                }
                                $tables = $normalizedTables;
                            }

                            $schemaPayload = [];
                            foreach ($tables as $t) {
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
                                                'comment' => $c['comment'] ?? '',
                                            ];
                                        } else {
                                            $cols[] = ['name' => (string)$c, 'type' => '', 'nullable' => false, 'key' => '', 'extra' => '', 'comment' => ''];
                                        }
                                    }
                                }
                                $schemaPayload[] = ['name' => $t['name'] ?? '', 'columns' => $cols, 'foreign_keys' => $t['foreign_keys'] ?? []];
                            }
                            $schemaJson = json_encode($schemaPayload, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
                            ?>


                            <?php

                            if (!empty($tables)):
                            ?>
                                <?php foreach ($tables as $table): ?>
                                    <tr>
                                        <td><?= Html::encode($table['name'] ?: '-') ?></td>
                                        <td>

                                            <!-- Table data modal -->
                                            <div class="modal fade" id="modal-table-data" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Table Preview </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div id="modal-table-message" class="mb-2"></div>
                                                            <div id="modal-unlinked-columns" class="mb-2"></div>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-bordered" id="modal-table-preview">
                                                                    <thead></thead>
                                                                    <tbody></tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="button" class="btn btn-sm btn-outline-primary btn-show-table" data-url="<?= Html::encode(Url::to(['system/table-data', 'id' => $model->id, 'table' => $table['name']])) ?>" data-table="<?= Html::encode($table['name']) ?>">View</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        <?php if ($status === 'error' || $status === 'warning'): ?>
                                            <div class="small text-muted"><?= Html::encode($message ?? 'Failed to retrieve structure from source database') ?></div>
                                        <?php else: ?>
                                            <div class="small text-muted">No tables available or no permissions assigned</div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>



<script src="/libs/sortablejs/Sortable.min.js"></script>
<?php
$this->registerJsFile('/react/bundle.js', ['position' => \yii\web\View::POS_END]);
// Register JS to handle click and fetch table data
$this->registerJs(
    <<<JS
    $(document).on('click', '.btn-show-table', function () {
    var btn = $(this);
    var url = btn.data('url');
    var tableName = btn.data('table') || btn.attr('data-table') || '';
    var modalEl = document.getElementById('modal-table-data');
    var modal = new bootstrap.Modal(modalEl);
    // set modal title to include table name
    try { $('#modal-table-data .modal-title').text('Table Preview: ' + (tableName || '')); } catch (e) {}
    $('#modal-table-message').text('Loading...');
    $('#modal-table-preview thead').empty();
    $('#modal-table-preview tbody').empty();
    modal.show();

    $.ajax({
        url: url,
        method: 'GET',
        dataType: 'json'
    }).done(function (res) {
            if (res.status === 'success') {
            var cols = res.data.columns || [];
            var rows = res.data.rows || [];
            var unlinked = res.data.unlinked_columns || [];
            var thead = $('#modal-table-preview thead');
            var tbody = $('#modal-table-preview tbody');
            var trh = $('<tr/>');
            cols.forEach(function (c) {
                var th = $('<th/>').text(c);
                if (unlinked.indexOf(c) !== -1) {
                    th.addClass('table-danger');
                }
                trh.append(th);
            });
            thead.append(trh);

            rows.forEach(function (r) {
                var tr = $('<tr/>');
                cols.forEach(function (c) {
                    var val = r[c];
                    if (val === null) val = 'NULL';
                    tr.append($('<td/>').text(String(val)));
                });
                tbody.append(tr);
            });

            var msg = rows.length + ' row(s) shown.';
            if (unlinked.length) {
                msg += ' Unlinked columns: ' + unlinked.join(', ');
                var list = $('<div/>').addClass('small text-danger').text('Unlinked: ' + unlinked.join(', '));
                $('#modal-unlinked-columns').empty().append(list);
            } else {
                $('#modal-unlinked-columns').empty();
            }

            $('#modal-table-message').text(msg);
        } else {
            $('#modal-table-message').text(res.message || 'Failed to load data');
        }
    }).fail(function (xhr) {
        var text = 'Request failed';
        try { text = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : xhr.statusText; } catch (e) {}
        $('#modal-table-message').text(text);
    });
});
JS
);

$this->registerJs(
    <<<JS
$(document).on('click', '.btn-show-cache', function () {
    var url = $(this).data('url');
    var modalEl = document.getElementById('modal-cache-data');
    var modal = new bootstrap.Modal(modalEl);

    $('#modal-cache-message').text('Loading cache data...').removeClass('text-danger').addClass('text-muted');
    $('#modal-cache-json').text('');
    modal.show();

    $.ajax({
        url: url,
        method: 'GET',
        dataType: 'json'
    }).done(function (res) {
        if (res.status === 'success') {
            var decodedRaw = [];
            if (Array.isArray(res.raw_data)) {
                decodedRaw = res.raw_data.map(function (item) {
                    return {
                        prefix: item.prefix || null,
                        file: item.file || null,
                        decode_type: item.decode_type || null,
                        decoded: (typeof item.decoded === 'undefined' ? null : item.decoded)
                    };
                });
            }

            var payload = {
                cache_info: res.cache_info || {},
                data: res.data || {},
                raw_cache_decoded: decodedRaw
            };

            if (Array.isArray(res.raw_data)) {
                $('#modal-cache-message').text('Raw cache loaded successfully.');
            } else {
                $('#modal-cache-message').text('Cache loaded successfully.');
            }
            $('#modal-cache-json').text(JSON.stringify(payload, null, 2));
        } else {
            $('#modal-cache-message').text(res.message || 'Failed to load cache data').removeClass('text-muted').addClass('text-danger');
            $('#modal-cache-json').text(JSON.stringify(res, null, 2));
        }
    }).fail(function (xhr) {
        var text = 'Request failed';
        try {
            text = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : xhr.statusText;
        } catch (e) {}

        $('#modal-cache-message').text(text).removeClass('text-muted').addClass('text-danger');
        $('#modal-cache-json').text('');
    });
});
JS
);
?>


<?php \richardfan\widget\JSRegister::begin(); ?>
<script>
    (function() {
        var schema = <?= $schemaJson ?> || [];
        var container = document.getElementById('schema-canvas-container');
        if (!container) return;

        var width = Math.max(container.clientWidth, 900);
        var height = Math.max(container.clientHeight, 480);

        var stage = new Konva.Stage({ container: 'schema-canvas-container', width: width, height: height });
        var layer = new Konva.Layer();
        stage.add(layer);
        var arrowsLayer = new Konva.Layer();
        stage.add(arrowsLayer);

        var paddingX = 20,
            paddingY = 20,
            boxWidth = 220,
            lineHeight = 18,
            headerHeight = 28;

        var colsPerRow = Math.max(1, Math.ceil(Math.sqrt(schema.length)));
        var groups = {};
        var links = [];

        // create groups with same styling as bridge/view
        schema.forEach(function(tbl, idx) {
            var row = Math.floor(idx / colsPerRow);
            var col = idx % colsPerRow;
            var x = paddingX + col * (boxWidth + paddingX);
            var cols = tbl.columns || [];
            var boxH = headerHeight + Math.max(1, cols.length) * lineHeight + 12;

            var w = boxWidth;
            var h = boxH;

            var group = new Konva.Group({ x: x, y: paddingY + row * (h + paddingY), draggable: true });

            var containerRect = new Konva.Rect({ x: 0, y: 0, width: w, height: h, fill: '#ffffff', cornerRadius: 6, shadowColor: '#000', shadowBlur: 6, shadowOffset: { x: 5, y: 5 }, shadowOpacity: 0.08 });
            var headerRect = new Konva.Rect({ x: 0, y: 0, width: w, height: headerHeight, fill: '#0d6efd', cornerRadius: 6 });
            var headerText = new Konva.Text({ x: 10, y: Math.max(2, (headerHeight - 14) / 2), text: tbl.name || '(table)', fontSize: 13, fontStyle: 'bold', fontFamily: 'Arial', fill: '#fff' });
            var sep = new Konva.Rect({ x: 0, y: headerHeight - 1, width: w, height: 1, fill: '#e9ecef' });

            group.add(containerRect);
            group.add(headerRect);
            group.add(sep);
            group.add(headerText);

            (cols || []).forEach(function(col, i) {
                var y = headerHeight + 6 + i * lineHeight;
                var rowBg = new Konva.Rect({ x: 0, y: y - 4, width: w, height: lineHeight + 6, fill: ((i % 2 === 0) ? '#ffffff' : '#fbfbfb') });
                var isPK = col.key && String(col.key).toUpperCase() === 'PRI';
                var text = (isPK ? 'PK ' : '') + (col.name || '') + (col.type ? ': ' + col.type : '') + (col.nullable ? '' : ' (NOT NULL)');
                var txt = new Konva.Text({ x: 10, y: y, text: text, fontSize: 12, fontFamily: 'Arial', fill: isPK ? '#c7254e' : '#333' });
                if (isPK) {
                    var circle = new Konva.Circle({ x: 6, y: y + lineHeight/2 - 1, radius: 4, fill: '#d9534f' });
                    group.add(circle);
                }
                group.add(rowBg);
                group.add(txt);
            });

            // interactions
            group.on('mouseover', function() { document.body.style.cursor = 'grab'; });
            group.on('mouseout', function() { document.body.style.cursor = ''; });
            group.on('dragstart', function() { this.moveToTop(); arrowsLayer.batchDraw(); });

            layer.add(group);
            var columnAnchors = {};
            (cols || []).forEach(function(col, i) {
                columnAnchors[col.name] = headerHeight + 6 + i * lineHeight + (lineHeight / 2);
            });

            groups[tbl.name] = { group: group, w: w, h: h, columnAnchors: columnAnchors };
        });

        function getColumnAnchor(tableName, columnName, preferredSide) {
            var table = groups[tableName];
            if (!table) return null;

            var anchorY = table.group.y() + (table.columnAnchors[columnName] || (headerHeight + 10));
            var anchorX = preferredSide === 'left'
                ? table.group.x() + 6
                : table.group.x() + table.w - 6;

            return { x: anchorX, y: anchorY };
        }

        // draw FK arrows similar to bridge/view
        schema.forEach(function(tbl) {
            var fks = tbl.foreign_keys || [];
            if (!Array.isArray(fks)) return;
            fks.forEach(function(fk) {
                var refTable = fk.referenced_table || fk.reference_table || fk.foreign_table || fk.table || null;
                if (!refTable) return;
                var src = groups[tbl.name];
                var dst = groups[refTable];
                if (!src || !dst) return;

                var sourceOnLeft = src.group.x() <= dst.group.x();
                var sourceAnchor = getColumnAnchor(tbl.name, fk.column, sourceOnLeft ? 'right' : 'left');
                var targetAnchor = getColumnAnchor(refTable, fk.referenced_column, sourceOnLeft ? 'left' : 'right');

                var sx = sourceAnchor ? sourceAnchor.x : src.group.x() + src.w - 6;
                var sy = sourceAnchor ? sourceAnchor.y : src.group.y() + headerHeight + 10;
                var dx = targetAnchor ? targetAnchor.x : dst.group.x() + 6;
                var dy = targetAnchor ? targetAnchor.y : dst.group.y() + headerHeight + 10;

                var arrow = new Konva.Arrow({ points: [sx, sy, dx, dy], pointerLength: 8, pointerWidth: 8, fill: '#666', stroke: '#666', strokeWidth: 1, lineJoin: 'round' });
                arrowsLayer.add(arrow);
                // store source/dest and arrow index for offsetting
                links.push({ arrow: arrow, srcName: tbl.name, dstName: refTable, srcColumn: fk.column || '', dstColumn: fk.referenced_column || '', idx: links.length });
            });
        });

        function computeBrokenPath(start, end, offsetIndex) {
            // produce an orthogonal (elbow) path from start to end
            var gap = 24; // minimal horizontal gap from box
            var offset = (offsetIndex % 3) * 8; // small offset to separate parallel links

            var sx = start.x, sy = start.y, dx = end.x, dy = end.y;
            // if source is left of destination, path: start -> midX -> end
            var midX;
            if (sx < dx) {
                midX = Math.max(sx + gap, Math.min(dx - gap, sx + (dx - sx) / 2));
            } else {
                midX = Math.min(sx - gap, Math.max(dx + gap, sx - (sx - dx) / 2));
            }
            // offset midX slightly per index to avoid exact overlap
            midX += (sx < dx ? 1 : -1) * offset;

            // build points: start -> (midX, sy) -> (midX, dy) -> end
            return [sx, sy, midX, sy, midX, dy, dx, dy];
        }

        function updateArrows() {
            links.forEach(function(l) {
                var src = groups[l.srcName];
                var dst = groups[l.dstName];
                if (!src || !dst) return;
                var sourceOnLeft = src.group.x() <= dst.group.x();
                var sourceAnchor = getColumnAnchor(l.srcName, l.srcColumn, sourceOnLeft ? 'right' : 'left');
                var targetAnchor = getColumnAnchor(l.dstName, l.dstColumn, sourceOnLeft ? 'left' : 'right');

                var sx = sourceAnchor ? sourceAnchor.x : src.group.x() + src.w - 6;
                var sy = sourceAnchor ? sourceAnchor.y : src.group.y() + headerHeight + 10;
                var dx = targetAnchor ? targetAnchor.x : dst.group.x() + 6;
                var dy = targetAnchor ? targetAnchor.y : dst.group.y() + headerHeight + 10;

                var pts = computeBrokenPath({ x: sx, y: sy }, { x: dx, y: dy }, l.idx || 0);
                l.arrow.points(pts);
            });
            arrowsLayer.batchDraw();
        }

        // attach drag listeners
        Object.keys(groups).forEach(function(name) {
            var g = groups[name].group;
            g.on('dragmove', updateArrows);
            g.on('dragend', updateArrows);
        });

        // initial draw
        layer.draw();
        updateArrows();

    })();
</script>
<?php \richardfan\widget\JSRegister::end(); ?>


<?php
// Initialize nested Sortable lists (handles: .handle)
$this->registerJs(
    <<<JS
(function(){
    function initNestedSortable(){
        $('.nested-list').each(function(){
            try {
                new Sortable(this, {
                    group: 'nested',
                    animation: 150,
                    handle: '.handle',
                    fallbackOnBody: true,
                    swapThreshold: 0.65
                });
            } catch (e) {
                console.warn('Sortable init failed', e);
            }
        });
    }

    if (typeof Sortable !== 'undefined') {
        initNestedSortable();
    } else {
        document.addEventListener('DOMContentLoaded', initNestedSortable);
    }
})();
JS
);
?>