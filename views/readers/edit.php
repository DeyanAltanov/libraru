<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Readers $model */

$this->title = 'Edit Profile: ';
?>
<div class="readers-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_edit_form', [
        'model' => $model,
    ]) ?>

</div>