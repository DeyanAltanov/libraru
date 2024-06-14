<?php

use app\models\ReservedBooks;
use app\models\RentedBooks;
use app\models\Books;
use app\models\Readers;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;

$this->title = 'This book has been rented by the following readers:';
$this->registerCssFile('@web/css/orders_2.css');
$books = RentedBooks::find()->andWhere(['ReturnedDate' => NULL])->andWhere(['BookID' => $model])->all();
?>
<div class="rented-by">
    <?php if(!empty($books)) {?>
        <h4 class='rented-by-h4'><?= Html::encode($this->title) ?></h4>
        <div class="grid-container">
            <div class="grid-item item1">Reader</div>
            <div class="grid-item item1">Amount</div>
            <div class="grid-item item2">Taken date</div>
            <div class="grid-item item4">Return date</div>

            <?php 
                foreach ($books as $index=>$data){
                    $reader = Readers::findOne(['ID' => $books[$index]['ReaderID']])?>
                    <div class="grid-item item5 <?= "row" . $index;?>" ><a href=<?= '/readers/view?ID='.$reader->ID ?> class='p_4'><?= $reader->FirstName ?> <?= $reader->LastName ?></a></div>
                    <div class="grid-item item6 <?= "row" . $index;?>" ><p><?= $books[$index]['Amount'] ?></p></div>
                    <div class="grid-item item7 <?= "row" . $index;?>" ><p><?= $books[$index]['TakenDate'] ?></p></div>
                    <div class="grid-item item8 <?= "row" . $index;?>" ><?= $books[$index]['ReturnDate'] ?></div>
                <?php }
            ?>
        </div>

    <?php }
    else{ ?>
        <h4 class='rented-by-h4'>This book is not rented by anyone at the moment.</h4>
    <?php
    }?>

</div>