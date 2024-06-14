<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Genres $model */

$this->title = 'Update genre: ';
?>
<div class="genres-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>