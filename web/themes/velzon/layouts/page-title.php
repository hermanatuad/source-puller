<!-- start page title -->
<?php
$breadcrumbs = isset($this->params['breadcrumbs']) && is_array($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [];
?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-transparent">
            <h4 class="mb-sm-0"><?= isset($title) ? \yii\helpers\Html::encode($title) : '' ?></h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <?php if (!empty($breadcrumbs)) : ?>
                        <?php foreach ($breadcrumbs as $i => $crumb) : ?>
                            <?php $isLast = ($i === array_key_last($breadcrumbs)); ?>
                            <?php if (is_string($crumb)) : ?>
                                <li class="breadcrumb-item<?= $isLast ? ' active' : '' ?>"><?= \yii\helpers\Html::encode($crumb) ?></li>
                            <?php elseif (is_array($crumb) && isset($crumb['label'])) : ?>
                                <?php $label = \yii\helpers\Html::encode($crumb['label']); ?>
                                <?php if (!$isLast && isset($crumb['url'])) : ?>
                                    <li class="breadcrumb-item"><a href="<?= \yii\helpers\Url::to($crumb['url']) ?>"><?= $label ?></a></li>
                                <?php else : ?>
                                    <li class="breadcrumb-item<?= $isLast ? ' active' : '' ?>"><?= $label ?></li>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li class="breadcrumb-item"><a href="javascript: void(0);"><?= isset($pagetitle) ? \yii\helpers\Html::encode($pagetitle) : '' ?></a></li>
                        <li class="breadcrumb-item active"><?= isset($title) ? \yii\helpers\Html::encode($title) : '' ?></li>
                    <?php endif; ?>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->