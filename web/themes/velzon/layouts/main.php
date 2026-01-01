<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use richardfan\widget\JSRegister;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;

?>
<?php $this->beginPage() ?>
<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-bs-theme="light" data-body-image="img-1" data-preloader="disable">

<head>

    <?php
    $metaTitle = isset($this->params['title']) ? $this->params['title'] : (isset($this->title) ? $this->title : '');
    echo $this->render('title-meta', ['title' => $metaTitle]);

    echo $this->render('head-css');
    ?>

</head>

<body>
    <?= Alert::widget() ?>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php echo $this->render('menu'); ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    <?php
                    $pagetitle = isset($this->params['pagetitle']) ? $this->params['pagetitle'] : '';
                    $title = isset($this->params['title']) ? $this->params['title'] : (isset($this->title) ? $this->title : '');
                    echo $this->render('page-title', ['pagetitle' => $pagetitle, 'title' => $title]);
                    ?>

                    <div class="row">
                        <?= $content ?>
                    </div>
                </div>
            </div>
            <?php echo $this->render('footer'); ?>
        </div>

    </div>

    <?php $this->render('customizer'); ?>

    <?php echo $this->render('vendor-scripts'); ?>

    <script src="/libs/prismjs/prism.js"></script>

    <!-- App js -->
    <script src="/js/app.js"></script>

</html>
<?php $this->endPage() ?>