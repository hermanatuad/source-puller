<?php

/** @var yii\web\View $this */
/** @var string $xmlContent */
/** @var string $xmlPath */
/** @var int|null $lastModifiedAt */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Patient XML Editor';
?>

<div class="col-12">
    <div class="card">
        <div class="card-header border-0 pb-0">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h5 class="card-title mb-1">Edit patient.xml</h5>
                    <p class="text-muted mb-0">
                        Update the XML source file directly from this page. The XML will be validated before it is saved.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-soft-secondary" href="<?= Url::to(['site/xml']) ?>" target="_blank" rel="noopener">
                        <i class="ri-external-link-line align-bottom me-1"></i> View Raw XML
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <div class="alert alert-info mb-0">
                        <strong>File:</strong> <?= Html::encode($xmlPath) ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-light mb-0 border">
                        <strong>Last updated:</strong>
                        <?= $lastModifiedAt ? Html::encode(Yii::$app->formatter->asDatetime($lastModifiedAt)) : 'Unknown' ?>
                    </div>
                </div>
            </div>

            <?= Html::beginForm(['site/xml-editor'], 'post') ?>
                <div class="mb-3">
                    <?= Html::label('XML Content', 'xml-content', ['class' => 'form-label fw-semibold']) ?>
                    <?= Html::textarea('xml_content', $xmlContent, [
                        'id' => 'xml-content',
                        'class' => 'form-control font-monospace',
                        'rows' => 36,
                        'spellcheck' => 'false',
                        'style' => 'white-space: pre; overflow-wrap: normal; overflow-x: auto;',
                    ]) ?>
                    <div class="form-text">
                        Saving will reformat the XML with consistent indentation.
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <?= Html::submitButton('<i class="ri-save-line align-bottom me-1"></i> Save XML', [
                        'class' => 'btn btn-primary',
                    ]) ?>
                    <a class="btn btn-light" href="<?= Url::to(['site/xml-editor']) ?>">
                        <i class="ri-refresh-line align-bottom me-1"></i> Reset
                    </a>
                </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>
