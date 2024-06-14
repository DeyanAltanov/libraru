<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\RentedBooksSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="rented-books-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ID') ?>

    <?= $form->field($model, 'BookID') ?>

    <?= $form->field($model, 'ReaderID') ?>

    <?= $form->field($model, 'TakenDate') ?>

    <?= $form->field($model, 'ReturnDate') ?>

    <?php // echo $form->field($model, 'ReturnedDate') ?>

    <?php // echo $form->field($model, 'Amount') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
