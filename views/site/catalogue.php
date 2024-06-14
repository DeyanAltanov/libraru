<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\ActiveForm;
use app\models\Books;
use app\models\Genres;
use app\models\BooksSearch;
if (isset($_GET['BooksSearch']['globalSearch'])){
    $search = $_GET['BooksSearch']['globalSearch'];
}
else {
    $search = '';
}

if (isset($_GET['genres'])) {
    $selected_genres = $_GET['genres'];
}
else {
    $selected_genres = null;
}

$genres = Genres::find()->all();
$genres_dropdown = [];
foreach ($genres as $index=>$genre){
    $genres_dropdown[$genre->ID] = $genre->Genre;
}

$this->registerCssFile('@web/css/catalogue.css');
$this->title = 'Catalogue';
?>

<div class="catalogue-page">
    <h1>Catalogue</h1>
</div>

<?php $form = ActiveForm::begin([
        'action' => ['search-books'],
        'options' => ['enctype'=>'multipart/form-data'],
        'method' => 'get',
    ]); ?>

    <?php $model = new BooksSearch(); ?>
    <?= $form->field($model, 'globalSearch')->textInput(['maxlength' => true, 'value' => $search])->label('Search book:') ?>
    <div class='btn-genres-drop-div'>
        <button type="button" id='drpdwn-btn' class="dropdown-btn form-control">Genres ▾</button>
        <?= $list = Html::checkboxList('genres', $selected_genres, $genres_dropdown, ['class' => 'genres-drop', 'id' => 'drpdwn-menu']); ?>
    </div>
    <?= Html::submitButton(Html::img(Yii::$app->request->baseUrl."/images/Search.jpg"), ['class' => 'btn search-book-submit-btn']) ?>

<?php ActiveForm::end(); ?>

<main>
    <?php foreach($books as $book) { ?>
        <section class="book">
            <?php
                $images = $book->Images; 
                $images = \yii\helpers\Json::decode($images);
                if (!empty($images)){?>
                    <img class="book-cover" src="<?= Yii::$app->request->baseUrl ?>/upload/<?=$book->ID?>/<?=$images[0]?>"/>
               <?php }
               else {?>
                    <img class="book-cover" src=""/>
                <?php }
            ?>
            <p class="book-title"><?=$book->Title?></p>
            <p class="book-author"><?=$book->Author?></p>
            <p class="amount"><strong>Amount: </strong><?=$book->CurrentAmount?>/<?=$book->TotalAmount?></p>
            <?= Html::a('Details', ['/books/view', 'ID' => $book->ID], ['class' => 'cat-btn', 'id'=>'cat-btn-det']) ?>
        </section> 
    <?php }?>
</main>
<?= LinkPager::widget([ 'pagination' => $pagination]) ?>
<script> 

    var coll = document.getElementsByClassName("dropdown-btn");
    var i;

    for (i = 0; i < coll.length; i++) {
        coll[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var content = this.nextElementSibling;
            if (content.style.display === "block") {
                content.style.display = "none";
            } else {
                content.style.display = "block";
            }
        });
    };

    document.addEventListener('click', function clickOutside(event) {
        let drpdwn_btn = document.getElementById('drpdwn-btn');
        let drpdwn_menu = document.getElementById('drpdwn-menu');
        if (!drpdwn_btn.contains(event.target)) {
           drpdwn_menu.style.display = 'none';
        }
    });
</script>