<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\CHtml;
use app\models\Genres;
use app\models\BookGenres;
use app\models\Books;
use wbraganca\dynamicform\DynamicFormWidget;

$this->registerCssFile('@web/css/form.css');

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

    <?= $form->field($model, 'TotalAmount')->textInput(['type' => 'number']) ?>

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
</script>