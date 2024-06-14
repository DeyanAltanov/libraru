<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->registerCssFile('@web/css/form.css');

/** @var yii\web\View $this */
/** @var app\models\Readers $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="password-update-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'OldPassword')->passwordInput(['value'=>'']) ?>

    <?= $form->field($model, 'NewPassword')->passwordInput() ?>

    <?= $form->field($model, 'RepeatNewPassword')->passwordInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-success']) ?>
        <?= Html::a(Html::img(Yii::$app->request->baseUrl."/images/Back.jpg"), ['edit', 'ID' => Yii::$app->user->identity->ID], ['class' => 'back']); ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>