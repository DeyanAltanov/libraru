<?php

use app\models\ReservedBooks;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use app\models\Books;
use app\models\Readers;
use yii\web\Session;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
$this->registerCssFile('@web/css/orders.css');
\yii\web\YiiAsset::register($this);
$this->title = 'Current Order';

?>
<div class="reserved-books-current-order">
    <?php if(!empty(Yii::$app->session->get('order'))) { ?>
        <?php $user = Readers::findOne(['ID' => Yii::$app->session->get('usr_id')]);
        if($_SESSION['__id'] != $_SESSION['usr_id']){?>
            <h1 id='order-h1'>Order for <?= $user->FirstName . ' ' . $user->LastName ?></h1>
        <?php }else { ?>
            <h1 id='order-h1'>Current order</h1>
        <?php } ?>
        <div class="grid-container">
            <div class="grid-item item1">Book</div>
            <div class="grid-item item2">Amount</div>
            <div class="grid-item item3">Remove</div>

            <?php 
                $order = Yii::$app->session->get('order');
                foreach ($order as $key=>$value) {
                    foreach ($value as $id=>$amount) {
                        $book = Books::findOne(['ID' => $id]);
                        $images = $book->Images; 
                        $images = \yii\helpers\Json::decode($images);
                        if (!empty($images)){
                            $image = Yii::$app->request->baseUrl.'/upload/'.$book->ID.'/'.$images[0];
                        }else {
                            $image = '';
                        }?>
                        <div class="grid-item item4 <?= "row" . $key; ?>"><img class="book-cover bcvr" src="<?= $image?>"/><a href=<?= '/books/view?ID='.$book->ID ?> class='p_1'><?= $book->Title ?></a></div>
                        <div class="grid-item item5 <?= "row" . $key; ?>"><input type="number"  id='book-amount' min=1 max=<?= $book->TotalAmount ?> value=<?= $amount ?>></div>
                        <div class="grid-item item6 <?= "row" . $key; ?>"><button  type='button' id='remove-book' onclick="deleteBook(<?= $key ?>)"><img src="<?= Yii::$app->request->baseUrl ?>/images/Delete.jpeg"></button></div>
                    <?php }
                }
             ?>
        </div>

        <div class='btns'>
            <?php $form = ActiveForm::begin(['action' => ['/reserved-books/submit-order'],'method' => 'post']) ?>
                <button type='submit' id='submit-order' class='btn btn-primary'>Submit</button>
            <?php ActiveForm::end(); ?>
            <?= Html::a('Delete', ['delete-order'], [
                'class' => 'btn btn-danger',
                'id' => 'delete-order',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this order?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    <?php
    }
    else{ 
        if (Yii::$app->session->get('usr_id') && Yii::$app->session->get('usr_id') != Yii::$app->session->get('__id')){
            $user = Readers::findOne(['ID' => Yii::$app->session->get('usr_id')]);?>
            <div class='empty'>
                <h1 id='order-h1'>Order for <?= $user->FirstName . ' ' . $user->LastName ?></h1>
                <p class='p-order-empty'>Order is empty.</p>
                <?= Html::a('Delete order', ['delete-order'], [
                    'class' => 'btn btn-danger btn-delete-empty',
                    'id' => 'delete-order',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this order?',
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
        <?php } 
        else {?>
        <h1 id='order-h1'>No orders</h1>
    <?php
        }
    }?>
</div>

<script>
function deleteBook(key) {
    var confirmation = confirm("Are you sure you want to remove this book from the order?");
    if (confirmation){
        $.ajax({
            type: 'post',
            url: '/reserved-books/clear-book-session?key=' + key,
            success: function(){
                $(".row" + key).remove();
                document.getElementById("cart-amount").innerHTML--;
                if(document.querySelectorAll('.book-cover').length == 0){
                    buttons = document.getElementsByClassName("btns");
                    buttons[0].style.display = 'none';
                }
            },
            error: function () {
                alert("Submission error.");
            }
        });
    }
    
};
</script>