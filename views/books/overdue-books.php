<?php

use app\models\ReservedBooks;
use app\models\RentedBooks;
use app\models\Books;
use app\models\Readers;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;

$this->title = 'Overdue Books';
$this->registerCssFile('@web/css/orders_2.css');
$books = RentedBooks::findAll(['ReturnedDate' => NULL]);
$empty = FALSE;
foreach ($books as $index=>$data){
    if(date($books[$index]['ReturnDate'], strtotime('-10 days')) < date('Y-m-d h:i:s')){
        $empty = TRUE;
        break;
    }
}
?>
<div class="books-overdue-books">
    <?php if($empty) {?>
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="grid-container">
            <div class="grid-item item1">Book</div>
            <div class="grid-item item1">Return Date</div>
            <div class="grid-item item2">Amount</div>
            <div class="grid-item item4">User</div>

            <?php 
                foreach ($books as $index=>$data){
                    $is_overdue = FALSE;
                    if(date($books[$index]['ReturnDate'], strtotime('-10 days')) < date('Y-m-d h:i:s')){
                        $is_overdue = TRUE;
                    }
                    if ($is_overdue == TRUE){
                        $book = Books::findOne(['ID' => $books[$index]['BookID']]);
                        $user = Readers::findOne(['ID' => $books[$index]['ReaderID']]);
                        $images = $book->Images; 
                        $images = \yii\helpers\Json::decode($images);
                        if (!empty($images)){
                            $image = Yii::$app->request->baseUrl.'/upload/'.$book->ID.'/'.$images[0];
                        }else {
                            $image = '';
                        }?>
                        <div class="grid-item item5 <?= "row" . $index;?> <?= $is_overdue ? "error-row" : "" ?>" ><img class="book-cover" src="<?= $image?>"/><p class='p_1'><?= $book->Title ?></p></div>
                        <div class="grid-item item6 <?= "row" . $index;?> <?= $is_overdue ? "error-row" : "" ?>" ><p class='p_2'><?= $books[$index]['ReturnDate'] ?></p></div>
                        <div class="grid-item item7 <?= "row" . $index;?> <?= $is_overdue ? "error-row" : "" ?>" ><p class='p_3'><?= $books[$index]['Amount'] ?></p></div>
                        <div class="grid-item item8 <?= "row" . $index;?> <?= $is_overdue ? "error-row" : "" ?>" ><a href=<?= '/readers/view?ID='.$user->ID ?> class='p_4'><?= $user->FirstName ?> <?= $user->LastName ?></a></div>
                <?php } 
                }
            ?>
        </div>

    <?php }
    else{ ?>
        <h4>No overdue books at the moment.</h4>
    <?php
    }?>

</div>