<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\CHtml;
use app\models\Genres;
use app\models\BookGenres;
use app\models\Books;
use wbraganca\dynamicform\DynamicFormWidget;

$this->registerCssFile('@web/css/form.css');
$this->registerJsFile('@web/js/edit_images.js');

/** @var yii\web\View $this */
/** @var app\models\Books $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="books-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype'=>'multipart/form-data']
    ]); ?>

    <?= $form->field($model, 'Title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Author')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ISBN')->textInput()->label('ISBN') ?>

    <?= $form->field($model, 'Images[]')->fileInput(['multiple' => true])->label('Upload images:') ?>

    <p>The first image(sorted by upload filename in descending order) will be used as cover!</p>

    <?= $form->field($model, 'TotalAmount')->textInput(['type' => 'number', 'min'=>$model->TotalAmount - $model->CurrentAmount]) ?>

    <p>NOTE: If a specific amount has been rented, it has to be returned, before deleted!</p>

    <?php
        $model_images = $model->Images;
        if($model_images != '[]'){ ?>
            <span>Current images:</span>
            <div class='model-images'>
            <?php
            $model_images = \yii\helpers\Json::decode($model_images);
            $index = 0;
            foreach ($model_images as $image) {?>
                <div class='image-and-buttons'>
                    <button type='button' class='swap-button swap-btn-left' name=<?= $index ?> id=<?= "left-btn" . $index;?> onclick="moveImage('left', <?= $index ?>)">
                        <img src="/images/Left.jpg" alt="">
                    </button>

                    <div class='model-image' id=<?= "img" . $index?>>
                        <img class='book-img' id=<?= "img-src" . $index;?> src="<?= Yii::$app->request->baseUrl ?>/upload/<?=$model->ID?>/<?=$image?>"/>
                        <input type="hidden" id=<?= "img" . $index . '-input'?>  name="CurrentImages[]" value="<?=$image?>"/>
                        <?php $image = "'". $image . "'"; ?>
                        <button type='button' id='remove-image' onclick="deleteImage(<?= $model->ID ?>, <?= $image ?>, <?= $index ?>)"><img src="<?= Yii::$app->request->baseUrl ?>/images/Delete.jpeg">
                    </div>

                    <button type='button' class='swap-button swap-btn-right' name=<?= $index ?> id=<?= "right-btn" . $index;?> onclick="moveImage('right', <?= $index ?>)">
                        <img src="/images/Right.jpg" alt="">
                    </button>
                </div>
            <?php $index++;}
            ?></div><?php
        }
    ?>

    <?= $form->field($model, 'Description')->textarea(['rows' => 6]) ?>

    <?php
    if($books = BookGenres::findAll(['BookID' => $model->ID])){
        ?><table class='book-genres'>
            <thead>
                <th>Genres</th>
            </thead>
            <tbody><?php
            foreach($books as $index=>$data){
                $genre = Genres::findOne(['ID' => $data->GenreID]);
                ?><tr id=<?= "row" . $index;?>>
                    <td><?= $genre->Genre ?></td>
                    <td><button type='button' id='remove-genre' onclick="deleteGenre(<?= $data->ID ?>, <?= $index ?>)"><img src="<?= Yii::$app->request->baseUrl ?>/images/Delete.jpeg"></button></td>
                </tr>
            <?php }
        }?>
        </tbody>
    </table>

    <div id='genres-dropdown' class="form-group">
        <input type="button" onclick="add_row();" value="Add Genre" id='genres-dropdown' class='form-control'>
        <?php $items = [];
            array_push($items, '');
            foreach ($genres as $index=>$genre){
                $items[$genre->ID] = $genre->Genre;
                $list = Html::dropDownList('genres[]', null, $items);
            }
        ?>

        <div id="genres-select">
            <?= $list ?>
        </div>

    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
function add_row() {
    const node = document.getElementById("genres-select").firstElementChild;
    const clone = node.cloneNode(true);
    clone.id = 'clone';
    let btn = document.createElement("button");
    btn.type = 'button';
    btn.id = 'delete-button';
    btn.style.marginRight = '8px';
    btn.style.marginBottom = '2px';
    btn.innerHTML = '<img src="<?= Yii::$app->request->baseUrl ?>/images/Delete.jpeg" />';
    btn.onclick = function(){
        $(clone).remove();
        $(btn).remove();
    }
    document.getElementById("genres-select").appendChild(clone);
    document.getElementById("genres-select").appendChild(btn);
}

function deleteGenre(id, index) {
    var confirmation = confirm("Remove this genre from the book?");
    if (confirmation){
        $.ajax({
            type: 'post',
            url: '/book-genres/delete-book-genre?id=' + id,
            success: function(){
                $("#row" + index).remove();
            },
            error: function () {
                alert("Submission error.");
            }
        });
    };
};

function deleteImage(id, image, index) {
    var confirmation = confirm("Remove this image?");
    if (confirmation){
        $.ajax({
            type: 'post',
            url: '/books/delete-book-image?id=' + id + '&img=' + image,
            success: function(){
                $("#img" + index).remove();
                $("#left-btn" + index).remove();
                $("#right-btn" + index).remove();
            },
            error: function () {
                alert("Submission error.");
            }
        });
    };
};

function moveImage(direction, index){
    if (direction == 'left'){
        var img_1_src = document.getElementById("img-src" + index);
        var img_2_src = document.getElementById("img-src" + (index - 1));
        var img_1 = document.getElementById("img" + index);
        var img_2 = document.getElementById("img" + (index - 1));
        var img_1_input = document.getElementById("img" + index + "-input");
        var img_2_input = document.getElementById("img" + (index - 1) + "-input");

        let first_image_src = img_1_src.src;
        let first_image = img_1.id;
        let img_1_input_value = img_1_input.value;

        img_1_src.src = img_2_src.src;
        img_2_src.src = first_image_src;
        img_1.id = img_2.id;
        img_2.id = first_image;
        img_1_input.value = img_2_input.value;
        img_2_input.value = img_1_input_value;
    }
    else {
        var img_1_src = document.getElementById("img-src" + index);
        var img_2_src = document.getElementById("img-src" + (index + 1));
        var img_1 = document.getElementById("img" + index);
        var img_2 = document.getElementById("img" + (index + 1));
        var img_1_input = document.getElementById("img" + index + "-input");
        var img_2_input = document.getElementById("img" + (index + 1) + "-input");

        let first_image_src = img_1_src.src;
        let first_image = img_1.id;
        let img_1_input_value = img_1_input.value;

        img_1_src.src = img_2_src.src;
        img_2_src.src = first_image_src;
        img_1.id = img_2.id;
        img_2.id = first_image;
        img_1_input.value = img_2_input.value;
        img_2_input.value = img_1_input_value;
    }
}
</script>