<?php

/** @var yii\web\View $this */
/** @var string $xmlPath */
/** @var string $xmlContent */
/** @var \DOMDocument|null $dom */
/** @var string[] $parseErrors */
/** @var int|null $lastModifiedAt */

use yii\helpers\Html;

$this->title = 'Patient New XML Viewer';

$extractDirectText = static function (\DOMElement $element): string {
    $parts = [];
    foreach ($element->childNodes as $childNode) {
        if (in_array($childNode->nodeType, [XML_TEXT_NODE, XML_CDATA_SECTION_NODE], true)) {
            $value = trim((string) $childNode->nodeValue);
            if ($value !== '') {
                $parts[] = $value;
            }
        }
    }

    return trim(implode(' ', $parts));
};

$renderElement = null;
$renderElement = static function (\DOMElement $element) use (&$renderElement, $extractDirectText): string {
    $attributes = [];
    if ($element->hasAttributes()) {
        foreach ($element->attributes as $attribute) {
            $attributes[] = Html::tag(
                'span',
                Html::encode($attribute->name . '="' . $attribute->value . '"'),
                ['class' => 'badge bg-soft-info text-info me-1 mb-1']
            );
        }
    }

    $children = [];
    foreach ($element->childNodes as $childNode) {
        if ($childNode instanceof \DOMElement) {
            $children[] = $childNode;
        }
    }

    $directText = $extractDirectText($element);
    $hasChildren = !empty($children);

    $html = '<li class="mb-2">';
    $html .= '<div class="p-2 border rounded bg-light-subtle">';
    $html .= Html::tag('span', Html::encode($element->tagName), ['class' => 'badge bg-primary me-2']);

    if (!empty($attributes)) {
        $html .= implode('', $attributes);
    }

    if ($directText !== '') {
        $html .= Html::tag(
            'div',
            Html::encode($directText),
            ['class' => 'mt-2 small text-break', 'style' => 'white-space: pre-wrap;']
        );
    }

    if (!$hasChildren && $directText === '') {
        $html .= Html::tag('div', '(empty)', ['class' => 'mt-2 small text-muted']);
    }

    $html .= '</div>';

    if ($hasChildren) {
        $html .= '<ul class="list-unstyled ms-3 mt-2 border-start ps-3">';
        foreach ($children as $child) {
            $html .= $renderElement($child);
        }
        $html .= '</ul>';
    }

    $html .= '</li>';

    return $html;
};
?>

<div class="col-12">
    <div class="card">
        <div class="card-header border-0 pb-0">
            <h5 class="card-title mb-1">Patient New XML Detail Viewer</h5>
            <p class="text-muted mb-0">
                Menampilkan seluruh struktur dan detail data dari file patient-new.xml.
            </p>
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

            <?php if (!empty($parseErrors)): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>XML parse error:</strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach ($parseErrors as $error): ?>
                            <li><?= Html::encode($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($dom !== null && $dom->documentElement !== null): ?>
                <div class="mb-4">
                    <h6 class="fw-semibold">Structured Detail</h6>
                    <p class="text-muted small mb-2">
                        Semua elemen XML ditampilkan secara hierarkis beserta atribut dan nilai datanya.
                    </p>
                    <ul class="list-unstyled">
                        <?= $renderElement($dom->documentElement) ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div>
                <h6 class="fw-semibold">Raw XML</h6>
                <pre class="border rounded p-3 bg-dark text-light mb-0" style="max-height: 720px; overflow: auto;"><code><?= Html::encode($xmlContent) ?></code></pre>
            </div>
        </div>
    </div>
</div>
