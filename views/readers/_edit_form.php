<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Readers;

$this->registerCssFile('@web/css/form.css');

/** @var yii\web\View $this */
/** @var app\models\Readers $model */
/** @var yii\widgets\ActiveForm $form */
$user_id = Yii::$app->user->ID;

$user = Readers::findOne(['ID' => $user_id]);

?>

<div class="edit-readers-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'FirstName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'LastName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Email')->textInput(['maxlength' => true]) ?>

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
        <?= Html::a('Change Password', ['/readers/change-password', 'ID' => Yii::$app->user->identity->ID], ['class' => 'btn btn-primary btn-profile change-password']) ?>
        <?= Html::a(Html::img(Yii::$app->request->baseUrl."/images/Back.jpg"), Yii::$app->request->referrer, ['class' => 'back']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>