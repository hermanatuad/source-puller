<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;

?>
<?php $this->beginPage() ?>
<?= Alert::widget() ?>
<?= $content ?>
<?php $this->endPage() ?>
