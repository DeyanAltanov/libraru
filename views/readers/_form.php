<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Readers;

/** @var yii\web\View $this */
/** @var app\models\Readers $model */
/** @var yii\widgets\ActiveForm $form */
$user_id = Yii::$app->user->ID;

$user = Readers::findOne(['ID' => $user_id]);
$this->registerCssFile('@web/css/form.css');
?>

<div class="readers-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'FirstName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'LastName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Password')->passwordInput(['maxlength' => true, 'value'=>'']) ?>

    <?= $form->field($model, 'RepeatPassword')->passwordInput(['maxlength' => true, 'value'=>'']) ?>

    <?= $form->field($model, 'Phone')->textInput(['maxlength' => true]) ?>

    <?php if ($user->Type == 'Admin'){?>
        <?php
            $value = 0;
            if ($model->Type == 'Librarian'){
                $value = 1;
            }
            else if ($model->Type == 'Admin'){
                $value = 2;
            }
        ?>
        <?= $form->field($model, 'Type')->dropDownList(['Reader', 'Librarian', 'Admin'], ['options' => [$value => ['selected' => true]]]) ?>
    <?php } ?>

    <?= $form->field($model, 'Address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ProfilePicture')->fileInput()->label('Profile picture:') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>