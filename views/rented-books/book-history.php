<?php

use app\models\ReservedBooks;
use app\models\RentedBooks;
use app\models\Books;
use app\models\Readers;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;

$this->title = 'Book History';
$this->registerCssFile('@web/css/orders_2.css');
$books = RentedBooks::find()->andWhere(['ReaderID' => $user_id])->andWhere(['ReturnDate' => NULL])->orderBy(['ReturnedDate' => SORT_DESC])->all();
?>
<div class="rented-books-book-history">
    <?php if(!empty($books)) {?>
        <h1><?= Html::encode($this->title) ?></h1>
        <div class="grid-container">
            <div class="grid-item item1">Book</div>
            <div class="grid-item item1">Amount</div>
            <div class="grid-item item2">Taken Date</div>
            <div class="grid-item item4">Returned Date</div>

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
                    <div class="grid-item item5 grid-his <?= "row" . $index;?>" ><img class="book-cover" src="<?= $image?>"/><a href=<?= '/books/view?ID='.$book->ID ?> class='p_1'><?= $book->Title ?></a></div>
                    <div class="grid-item item6 grid-his <?= "row" . $index;?>" ><p class='p_3'><?= $books[$index]['Amount'] ?></p></div>
                    <div class="grid-item item7 grid-his <?= "row" . $index;?>" ><p class='p_1'><?= $books[$index]['TakenDate'] ?></p></div>
                    <div class="grid-item item8 grid-his <?= "row" . $index;?>" ><p class='p_1'><?= $books[$index]['ReturnedDate'] ?></p></div>
                <?php }
            ?>
        </div>

    <?php }
    else{ ?>
        <h4>Book history is empty.</h4>
    <?php
    }?>

</div>