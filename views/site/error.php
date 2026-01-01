<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

// Handle both $exception and $name/$message variables
if (isset($exception)) {
    $name = $exception->getName();
    $message = $exception->getMessage();
    $statusCode = $exception->statusCode ?? 500;
} else {
    $statusCode = 500;
}

$this->title = $name;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 shadow-none">
            <div class="card-body text-center">
                <div class="mb-4">
                    <?php if ($statusCode == 403): ?>
                        <i class="ri-shield-cross-line text-danger display-1"></i>
                    <?php elseif ($statusCode == 404): ?>
                        <i class="ri-file-search-line text-warning display-1"></i>
                    <?php else: ?>
                        <i class="ri-error-warning-line text-danger display-1"></i>
                    <?php endif; ?>
                </div>
                
                <h1 class="display-5 fw-bold mb-3"><?= Html::encode($this->title) ?></h1>
                
                <div class="alert alert-<?= $statusCode == 403 ? 'warning' : 'danger' ?> alert-border-left alert-dismissible fade show" role="alert">
                    <i class="ri-alert-line me-3 align-middle fs-16"></i>
                    <strong><?= Html::encode($message) ?></strong>
                </div>

                <?php if ($statusCode == 403): ?>
                    <p class="text-muted mb-4">
                        You don't have permission to access this resource. Please contact your administrator if you believe this is an error.
                    </p>
                <?php else: ?>
                    <p class="text-muted mb-4">
                        The above error occurred while the Web server was processing your request.
                        Please contact us if you think this is a server error. Thank you.
                    </p>
                <?php endif; ?>

                <div class="mt-4">
                    <?= Html::a('<i class="ri-home-4-line me-1"></i> Back to Home', ['/site/index'], [
                        'class' => 'btn btn-primary'
                    ]) ?>
                    
                    <?php if (!Yii::$app->user->isGuest): ?>
                        <?= Html::a('<i class="ri-arrow-left-line me-1"></i> Go Back', 'javascript:history.back()', [
                            'class' => 'btn btn-secondary'
                        ]) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
