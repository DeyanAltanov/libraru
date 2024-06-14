<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ReservedBooks $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="reserved-books-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'BookID')->textInput() ?>

    <?= $form->field($model, 'ReaderID')->textInput() ?>

    <?= $form->field($model, 'Date')->textInput() ?>

    <?= $form->field($model, 'Amount')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
