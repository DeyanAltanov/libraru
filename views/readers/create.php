<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Readers $model */

$this->title = 'New User';

?>
<div class="readers-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>