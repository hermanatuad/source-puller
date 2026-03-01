<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Affiliation $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="affiliation-form">

    <?php $form = ActiveForm::begin(['options' => ['class' => 'needs-validation']]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'affiliation_code')->textInput(['maxlength' => true, 'placeholder' => 'Affiliation code']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'affiliation_name')->textInput(['maxlength' => true, 'placeholder' => 'Affiliation name']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'address')->textInput(['maxlength' => true, 'placeholder' => 'Address']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'placeholder' => 'Phone']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'email')->input('email', ['maxlength' => true, 'placeholder' => 'Email']) ?>
        </div>
    </div>

    <div class="mt-3">
        <?= Html::submitButton('<i class="ri-save-line align-bottom me-1"></i> Save', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="ri-close-line align-bottom me-1"></i> Cancel', ['index'], ['class' => 'btn btn-secondary ms-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Affiliation $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="affiliation-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->hiddenInput(['value' => $uuid ?? $model->id])->label(false) ?>

    <?= $form->field($model, 'affiliation_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'affiliation_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
    <br>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>