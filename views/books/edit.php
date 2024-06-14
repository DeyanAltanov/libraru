<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Books $model */

$this->title = 'Edit Book: ';
?>
<div class="books-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_edit_form', [
        'model' => $model,
        'genres' => $genres,
    ]) ?>

</div>