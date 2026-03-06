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
                    // Build a simple relation graph using column name heuristics (columns ending with _id)
                    $nodes = [];
                    $edges = [];
                    $tableNames = array_keys($tables);

                    foreach ($tables as $tname => $meta) {
                        $cols = $meta['columns'] ?? [];
                        $label = $tname . "\n(" . (count($cols)) . " cols)";
                        $id = 'n_' . md5($tname);
                        $nodes[$tname] = ['id' => $id, 'label' => $label];
                    }

                    foreach ($tables as $tname => $meta) {
                        $cols = $meta['columns'] ?? [];
                        foreach ($cols as $c) {
                            $colName = $c['name'] ?? '';
                            if (preg_match('/^([a-zA-Z0-9]+)_id$/', $colName, $m)) {
                                $ref = $m[1];
                                // if referenced table exists, create edge
                                if (in_array($ref, $tableNames, true)) {
                                    $edges[] = [$tname, $ref, $colName];
                                }
                            }
                        }
                    }

                    // create mermaid graph definition (sanitize labels to avoid syntax errors)
                    $mermaid = "graph LR\n";
                    foreach ($nodes as $t => $n) {
                        // sanitize label: remove problematic characters and newlines
                        $safeLabel = str_replace(["\\", '"', "\n", "|", "[", "]"], ['', '', ' ', ' ', ' ', ' '], $n['label']);
                        $mermaid .= $n['id'] . '["' . $safeLabel . '"]\n';
                    }
                    foreach ($edges as $e) {
                        list($from, $to, $col) = $e;
                        $fromId = $nodes[$from]['id'];
                        $toId = $nodes[$to]['id'];
                        // sanitize edge label
                        $safeEdge = str_replace(["\\", '"', "|", "\n"], ['', '', ' ', ' '], $col);
                        $mermaid .= "$fromId --|" . $safeEdge . "| $toId\n";
                    }
                ?>

                    <div id="dw-graph">
                        <div class="mb-2 small text-muted">Click nodes to open schema view.</div>
                        <div class="mermaid" id="dwMermaid">
                            <?= $mermaid ?>
                        </div>
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            if (typeof mermaid !== 'undefined') {
                                mermaid.initialize({
                                    startOnLoad: true,
                                    theme: 'default'
                                });

                                // add click handlers: map node ids back to table names
                                var mapping = {};
                                <?php foreach ($nodes as $t => $n): ?>
                                    mapping['<?= $n['id'] ?>'] = '<?= addslashes($t) ?>';
                                <?php endforeach; ?>

                                // delegate click events on SVG nodes
                                setTimeout(function() {
                                    var svg = document.querySelector('#dwMermaid svg');
                                    if (!svg) return;
                                    svg.addEventListener('click', function(ev) {
                                        var target = ev.target;
                                        // walk up to group with id starting with 'node-'
                                        while (target && target !== svg) {
                                            if (target.id && target.id.indexOf('n_') === 0) {
                                                var tid = target.id;
                                                var tname = mapping[tid];
                                                if (tname) {
                                                    window.location = '<?= Url::to(['datawarehouse/view']) ?>?table=' + encodeURIComponent(tname);
                                                }
                                                return;
                                            }
                                            target = target.parentElement;
                                        }
                                    }, false);
                                }, 500);
                            }
                        });
                    </script>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>