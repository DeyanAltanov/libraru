<?php

use app\models\ReservedBooks;
use app\models\RentedBooks;
use app\models\Books;
use app\models\Readers;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;

$this->title = 'Taken Books';
$this->registerCssFile('@web/css/orders_3.css');
$books = RentedBooks::findAll(['ReaderID' => $user_id, 'ReturnedDate' => NULL]);
$user_id = Yii::$app->user->ID;
$user = Readers::findOne(['ID' => $user_id]);

?>
<div class="rented-books-taken-books">
    <?php if(!empty($books)) {?>
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="grid-container">
            <div class="grid-item item1">Book</div>
            <div class="grid-item item2">Amount</div>
            <div class="grid-item item3">Taken Date</div>
            <div class="grid-item item4">Return Date</div>
            <div class="grid-item item5"><p>Return</p></div>


            <?php 
                foreach ($books as $index=>$data){
                    $is_overdue = FALSE;
                    if(date($books[$index]['ReturnDate'], strtotime('-10 days')) < date('Y-m-d h:i:s')){
                        $is_overdue = TRUE;
                    }
                    $book = Books::findOne(['ID' => $books[$index]['BookID']]);
                    $images = $book->Images; 
                    $images = \yii\helpers\Json::decode($images);
                    if (!empty($images)){
                        $image = Yii::$app->request->baseUrl.'/upload/'.$book->ID.'/'.$images[0];
                    }else {
                        $image = '';
                    }?>
                    <div class="grid-item item6 <?= "row" . $index;?> <?= $is_overdue ? "error-row" : "" ?>" ><img class="book-cover" src="<?= $image?>"/><a href=<?= '/books/view?ID='.$book->ID ?> class='p_2'><?= $book->Title ?></a></div>
                    <div class="grid-item item7 <?= "row" . $index;?> <?= $is_overdue ? "error-row" : "" ?>" ><p class='p_1'><?= $books[$index]['Amount'] ?></p></div>
                    <div class="grid-item item8 <?= "row" . $index;?> <?= $is_overdue ? "error-row" : "" ?>" ><p class='p_2'><?= $books[$index]['TakenDate'] ?></p></div>
                    <div class="grid-item item9 <?= "row" . $index;?> <?= $is_overdue ? "error-row" : "" ?>" ><p class='p_2'><?= $books[$index]['ReturnDate'] ?></p></div>
                    <div class="grid-item item10 <?= "row" . $index;?> <?= $is_overdue ? "error-row" : "" ?>" ><button type='button' id='return-book' onclick="returnBook(<?= $books[$index]['ID'] ?>, <?= $index ?>)"><img class='tick' src="<?= Yii::$app->request->baseUrl ?>/images/Tick.jpg"></button></div>
                    <?php if ($user->Type == 'Reader'){?>
                        <style>
                            .item10, .item5 {
                                border-top: 0 !important;
                                border-right: 0 !important;
                                border-bottom: 0 !important;
                                background: white;
                            }
                            #return-book, .item5 > p {
                                display: none;
                            }
                            .grid-container {
                                margin-right: 150px;
                            }
                            @media(max-width:1399px) {
                                .grid-container {
                                    margin-right: 70px !important;
                                }
                                .legend {
                                    margin-left: 105px !important;
                                }
                            }
                            @media(max-width:1191px) {
                                .grid-container {
                                    margin-right: 2px !important;
                                }
                                .legend {
                                    margin-left: 9px !important;
                                }
                            }
                        </style>
                    <?php }
                }
            ?>
        </div>

        <div class='legend'>
            <img src="<?= Yii::$app->request->baseUrl ?>/images/Warning.jpg" class='warning'> - overdue
        </div>
    <?php }
    else{ ?>
        <h4>The user hasn't rented any books at the moment.</h4>
    <?php
    }?>

</div>

<script>
   function returnBook(id, index) {
        var confirmation = confirm("Return book?");
        if (confirmation){
            $.ajax({
                type: 'post',
                url: '/rented-books/return-book?id=' + id,
                success: function(){
                    $(".row" + index).remove();
                    if(document.querySelectorAll('.book-cover').length == 0){
                        legend = document.getElementsByClassName("legend");
                        legend[0].style.display = 'none';
                    }
                },
                error: function () {
                    alert("Submission error.");
                }
            });
        };
    };
</script>