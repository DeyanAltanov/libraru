<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Readers $model */

$this->title = 'Change Password: ';
?>
<div class="change-password">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_change_password_form', [
        'model' => $model,
    ]) ?>

</div>