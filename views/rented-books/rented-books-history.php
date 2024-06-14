<?php

use app\models\RentedBooks;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\RentedBooksSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Rented Books History';
?>
<div class="rented-books-rented-books-history">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'BookID',
            'ReaderID',
            'TakenDate',
            'ReturnDate',
            'ReturnedDate',
            'Amount',
        ],
    ]); ?>


</div>