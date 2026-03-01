<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Affiliation $model */

$this->title = 'Create Affiliation';
$this->params['breadcrumbs'][] = ['label' => 'Affiliations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0"><i class="ri-add-line me-2"></i><?= Html::encode($this->title) ?></h4>
            </div>
            <div class="card-body">
                <?= $this->render('_form', ['model' => $model]) ?>
            </div>
        </div>
    </div>
</div>
<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Affiliation $model */

$this->title = 'Create Affiliation';
$this->params['breadcrumbs'][] = ['label' => 'Affiliations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="affiliation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'uuid' => $uuid ?? null,
    ]) ?>

</div>
