<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use richardfan\widget\JSRegister;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\web\YiiAsset;

// Register AppAsset and YiiAsset to enable CSS/JS and CSRF handling
AppAsset::register($this);
YiiAsset::register($this);

?>
<?php $this->beginPage() ?>
<!doctype html>
<?php echo $this->render('setting'); ?>

<head>

    <?php JSRegister::begin(); ?>
    <script>
        (function() {
            try {
                var key = 'velzonTheme';
                var stored = null;
                try {
                    stored = localStorage.getItem(key);
                } catch (e) {
                    stored = null;
                }
                if (stored) {
                    document.documentElement.setAttribute('data-bs-theme', stored);
                    try {
                        sessionStorage.setItem('data-bs-theme', stored);
                    } catch (e) {}
                }
            } catch (e) {}
        })();
    </script>
    <?php JSRegister::end(); ?>

    <?php
    $metaTitle = isset($this->params['title']) ? $this->params['title'] : (isset($this->title) ? $this->title : '');
    echo $this->render('title-meta', ['title' => $metaTitle]);

    echo $this->render('head-css');
    ?>

    <?php $this->head(); ?>
</head>

<body>
    <?php $this->beginBody(); ?>

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
                        
                        <?= Alert::widget() ?>

                        <?= $content ?>
                    </div>
                </div>
            </div>
            <?php echo $this->render('footer'); ?>
        </div>

    </div>

    <?php $this->render('customizer'); ?>

    <?php echo $this->render('vendor-scripts'); ?>

    <script src="/js/pages/form-wizard.init.js"></script>
    <script src="/libs/prismjs/prism.js"></script>

    <!-- App js -->
    <script src="/js/app.js"></script>

    <?php $this->endBody(); ?>

</html>
<?php $this->endPage() ?>