<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Genres $model */

$this->title = 'Add new genre:';
?>
<div class="genres-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>