<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\ContactForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\captcha\Captcha;

$this->title = 'Contact Info';
?>
<div class='contact'>
    <h1><?= Html::encode($this->title) ?></h1>

    <div class='contact-info'>
        <p><strong>Phone: </strong>(+359)888-123-456</p>
        <p><strong>Hours of operation: </strong>Monday - Friday: 08:30 - 19:45 / Saturday - Sunday: 09:00 - 14:45</p>
        <p><strong>Address: </strong>Studentski grad, street: "Akademic St. Mladenov" 1, 1700 Sofia</p>
    </div>
</div>