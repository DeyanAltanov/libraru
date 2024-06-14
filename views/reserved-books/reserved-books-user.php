<?php

use app\models\ReservedBooks;
use app\models\Books;
use app\models\Readers;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;

$this->title = 'Reserved books';
$this->registerCssFile('@web/css/orders_2.css');
$user_id = $user;

$user = Readers::findOne(['ID' => Yii::$app->user->ID]);
?>
<div class="reserved-books-reserved-books-user">

    <?php if(!empty($books)) {?>
        <h1><?= Html::encode($this->title) ?></h1>
        <div class="grid-container">
            <div class="grid-item item1">Book</div>
            <div class="grid-item item1">Date</div>
            <div class="grid-item item2">Amount</div>
            <div class="grid-item item4">Remove</div>

            <?php 
                foreach ($books as $index=>$data){
                    $book = Books::findOne(['ID' => $books[$index]['BookID']]);
                    $images = $book->Images; 
                    $images = \yii\helpers\Json::decode($images);
                    if (!empty($images)){
                        $image = Yii::$app->request->baseUrl.'/upload/'.$book->ID.'/'.$images[0];
                    }else {
                        $image = '';
                    }?>
                    <div class="grid-item item5 <?= "row" . $index;?>" ><img class="book-cover" src="<?= $image?>"/><a href=<?= '/books/view?ID='.$book->ID ?> class='p_1'><?= $book->Title ?></a></div>
                    <div class="grid-item item6 <?= "row" . $index;?>" ><p class='p_2'><?= $books[$index]['Date'] ?></p></div>
                    <div class="grid-item item7 <?= "row" . $index;?>" ><p class='p_3'><?= $books[$index]['Amount'] ?></p></div>
                    <div class="grid-item item8 <?= "row" . $index;?>" ><button type='button' id='remove-book' onclick="deleteBook(<?= $books[$index]['ID'] ?>, <?= $index ?>)"><img src="<?= Yii::$app->request->baseUrl ?>/images/Delete.jpeg"></button></div>
                <?php }
            ?>
        </div>
        <div class='btns'>
            <?php if ($user->Type != 'Reader'){?>
                <?php $form = ActiveForm::begin(['action' => ['/rented-books/approve-order?ID=' . $user_id],'method' => 'post']) ?>
                    <button type='submit' class='btn btn-primary' id='btn-approve'>Approve</button>
                <?php ActiveForm::end(); ?>
            <?php } ?>
            <?= Html::a('Cancel', ['cancel-order', 'ID' => $user_id], [
                'class' => 'btn btn-danger',
                'id' => 'delete-order',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this order?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>

    <?php }
    else{ ?>
        <h4>The user hasn't reserved any books at the moment.</h4>
    <?php
    }?>
</div>

<script>
   function deleteBook(id, index) {
        var confirmation = confirm("Are you sure you want to remove this book from the order?");
        if (confirmation){
            $.ajax({
                type: 'post',
                url: '/reserved-books/delete-reserved-book?id=' + id,
                success: function(){
                    $(".row" + index).remove();
                    if(document.querySelectorAll('.book-cover').length == 0){
                        buttons = document.getElementsByClassName("btns");
                        buttons[0].style.display = 'none';
                    }
                },
                error: function () {
                    alert("Submission error.");
                }
            });
        };
    };
</script>