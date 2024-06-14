<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\BookGenres;
use app\models\Genres;
use app\models\Readers;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\Session;

/** @var yii\web\View $this */
/** @var app\models\Books $model */
$this->registerCssFile('@web/css/view.css');
$this->registerJsFile('@web/js/carousel.js');

$user_id = Yii::$app->user->ID;

$user = Readers::findOne(['ID' => $user_id]);

\yii\web\YiiAsset::register($this);
?>

<div class="book-view">
    <section class="book-info">
        <div class="book-info-1">
            <?php
                $images = $model->Images; 
                $images = \yii\helpers\Json::decode($images);
                if (!empty($images)){?>
                    <div class='carousel'>
                        <button class='carousel-button carousel-btn-left is-hidden'>
                            <img src="<?= Yii::$app->request->baseUrl ?>/images/Left.jpg" alt="">
                        </button>

                        <div class='carousel_track-container'>
                            <ul class='carousel_track'>
                                <?php foreach ($images as $image) {?>
                                <li class='carousel_slide current-slide'>
                                    <img class='carousel-image' src="<?= Yii::$app->request->baseUrl ?>/upload/<?=$model->ID?>/<?=$image?>"/>
                                </li>
                                <?php }?>
                            </ul>
                        </div>

                        <?php if (count($images) > 1){?>
                            <button class='carousel-button carousel-btn-right'>
                                <img src="<?= Yii::$app->request->baseUrl ?>/images/Right.jpg" alt="">
                            </button>
                        <?php }?>
                        <div class='carousel-nav'>
                            <?php for ($i=1; $i<=count($images); $i++) {?>
                                <button class='carousel_indicator current-slide'></button>
                            <?php }?>
                        </div>
                    </div>
               <?php 
               }
               else {?>
                    <img class='no-image' src="<?= Yii::$app->request->baseUrl ?>/images/No Image.jpg" alt="">
                <?php }
            ?>

            <div class='book-details'>
                <h1><?= $model->Title ?></h1>
                <h3><?= $model->Author ?></h3>
                <p class='description'><?= $model->Description ?></p>
            </div>
        </div>
        <div class="book-info-2">
            <div class="book-info-2-2">
                <a href=<?= Yii::$app->request->referrer ?> class='back book-back'>
                    <img src="<?= Yii::$app->request->baseUrl ?>/images/Back.jpg">
                </a>
                <div class='isbn-av'>
                    <p ><strong class='strong isbn'>ISBN:</strong> <?= $model->ISBN ?> </p>
                    <p ><strong class='strong'>Availability:</strong> <?=$model->CurrentAmount?>/<?=$model->TotalAmount?></p>
                </div>
                <?php
                    if($books = BookGenres::findAll(['BookID' => $model->ID])){
                        ?><div class='book-genres'>
                            <p><strong class='strong'>Genres:</strong></p><?php
                            $i = 1;
                            foreach($books as $book){
                                $genre = Genres::findOne(['ID' => $book->GenreID]);
                                $str = $genre->Genre . ',';
                                if ($i == count($books)){
                                    $str = substr($str, 0, -1);
                                }
                                ?><p><?= $str ?> </p>
                            <?php
                            $i++;
                            }?></div>
                      <?php  }?>
                    
            </div>
            <?php if (!Yii::$app->user->isGuest){
                if($model->CurrentAmount != 0) {?>
                    <?php $form = ActiveForm::begin(['action' => ['reserved-books/submit-amount'], 'method' => 'post', 'options' => [ 'class' => 'amount-form' ]]) ?>
                        <?= $form->field($model, 'Amount', ['options' => ['class' => 'amount']])->input('number', ['min'=>1,'max'=>$model->CurrentAmount, 'value'=>1]) ?>
                        <?= $form->field($model, 'BookID')->hiddenInput(['value'=> $model->ID])->label(false) ?>
                        <button type='submit' class='add'><img src="<?= Yii::$app->request->baseUrl ?>/images/Add.jpg"></button>
            <?php ActiveForm::end(); } }?>
        </div>
    </section>
    <section class='buttons book-btns'>
        <?php if (!Yii::$app->user->isGuest && $user->Type != 'Reader'){?>
            <div class="btns">
                <?= Html::a('Edit', ['edit', 'ID' => $model->ID], ['class' => 'btn btn-primary bk-edit']) ?>
                <?= Html::a('Rented By:', ['/books/rented-by', 'ID' => $model->ID], ['class' => 'btn btn-primary rented-by']) ?>
                <?php $form = ActiveForm::begin(['action' => ['readers/all-users'], 'method' => 'post', 'options' => ['class' => 'give-to-user']]) ?>
                    <?= $form->field($model, 'BookID')->hiddenInput(['value'=> $model->ID])->label(false) ?>
                    <button type='submit' class='btn btn-primary give-to-user-btn'>Give to user</button>
                <?php ActiveForm::end(); ?>
                <?= Html::a('Delete', ['delete', 'ID' => $model->ID], [
                    'class' => 'btn btn-danger bk-delete',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this book?',
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
        <?php } ?>
    </section>
</div>