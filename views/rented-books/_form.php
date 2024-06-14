<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\RentedBooks $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="rented-books-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'BookID')->textInput() ?>

    <?= $form->field($model, 'ReaderID')->textInput() ?>

    <?= $form->field($model, 'TakenDate')->textInput() ?>

    <?= $form->field($model, 'ReturnDate')->textInput() ?>

    <?= $form->field($model, 'ReturnedDate')->textInput() ?>

    <?= $form->field($model, 'Amount')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>