<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Readers;

/** @var yii\web\View $this */
/** @var app\models\Readers $model */
$this->registerCssFile('@web/css/view.css');
\yii\web\YiiAsset::register($this);
$user_id = Yii::$app->user->ID;

$user = Readers::findOne(['ID' => $user_id]);

?>
<div class="readers-view">

    <section class="reader-info">
        <div class="reader-info-1">
            <a href=<?= Yii::$app->request->referrer ?> class='back'><img src="<?= Yii::$app->request->baseUrl ?>/images/Back.jpg"></a>
            <img src="<?= Yii::$app->request->baseUrl ?>/profile_pictures/<?=$model->ID?>/<?=$model->ProfilePicture?>" class='profile_picture'/>
        </div>

        <div class="reader-info-2">
            <h2><?= $model->FirstName ?> <?= $model->LastName ?></h2>
            <p><strong class='strong'>Email:</strong> <?= $model->Email ?></p>
            <p><strong class='strong'>Phone number:</strong> <?= $model->Phone ?></p>
            <p class='usr-addr'><strong class='strong address'>Address:</strong> <?= $model->Address ?></p>
        </div>
        
    </section>

    <?php if ($user->Type != 'Reader'){?>
        <section class='comments'>
            <?php $comments = $model->Comments;
                $comments = \yii\helpers\Json::decode($comments);

                if ($comments){ ?>
                    <table class='comments-table'>
                        <?php foreach($comments as $key => $value){
                            foreach($value as $sender_id => $comment){
                                $sender = Readers::findOne(['ID' => $sender_id]);?>
                                <tr id=<?= "row" . $key; ?>>
                                    <td class='sender'><?= $sender->FirstName . ' ' .$sender->LastName . ': ' ?></td>
                                    <td class='comment'><?= $comment ?></td>
                                    <td>
                                        <button type='button' id='remove-comment' onclick="deleteComment(<?= $model->ID ?>, <?= $key ?>)"><img src="<?= Yii::$app->request->baseUrl ?>/images/Delete.jpeg"></button>
                                    </td>
                                </tr>
                            <?php }
                        }?>
                    </table>
                    <?php }

            ?>
        </section>
    <?php }?>

    <section class='buttons reader-btns'>
        <?= Html::a('Edit profile', ['edit', 'ID' => $model->ID], ['class' => 'btn btn-primary btn-profile edit']); ?>
        <?= Html::a('Reserved books', ['reserved-books/reserved-books-user', 'userID' => $model->ID], ['class' => 'btn btn-primary btn-profile reserved-books']) ?>
        <?= Html::a('Book history', ['/rented-books/book-history', 'userID' => $model->ID], ['class' => 'btn btn-primary btn-profile book-history']) ?>
        <?= Html::a('Taken books', ['/rented-books/taken-books', 'userID' => $model->ID], ['class' => 'btn btn-primary btn-profile taken-books']) ?>
    </section>

    <?php if ($user->Type != 'Reader'){?>
        <section class='admin-btns'>
            <button href='' class='comment-btn' id="add-comment">
                <img src="<?= Yii::$app->request->baseUrl ?>/images/Comment.jpg">
            </button>

            <form id='comment-form' method='get' action='/readers/send-comment'>
                <div class='form-comments'>
                    <textarea rows="2" cols="25" name="txtcomment" form='comment-form'></textarea>
                    <input type="hidden" id="sender-id" name="id-sender" value=<?= $_SESSION['__id'] ?> />
                    <input type="hidden" id="user-id" name="id-user" value=<?= $model->ID ?> />
                    <input type="submit" class='btn btn-primary send-comment-btn' value="Add" />
                </div>
            </form>

            <?php if ($model->Active == True){?>
                <?= Html::a(Html::img('@web/images/Suspend.jpg'), ['suspend-user', 'ID' => $model->ID], [
                    'class' => 'suspend',
                    'data' => [
                        'confirm' => 'Are you sure you want to suspend this user?',
                        'method' => 'post',
                    ],
                ]);
                } else { ?>
                <?= Html::a(Html::img('@web/images/Unsuspend.jpg'), ['unsuspend-user', 'ID' => $model->ID], [
                    'class' => 'suspend',
                    'data' => [
                        'confirm' => 'Unblock user?',
                        'method' => 'post',
                    ],
                ]);
            }?>

            <?php if ($user->Type == 'Admin'){?>
                <?= Html::a('Delete user', ['delete', 'ID' => $model->ID], [
                        'class' => 'btn btn-danger delete-usr',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this book?',
                            'method' => 'post',
                        ],
                    ]) ?>
            <?php } ?>
        </section>
    <?php }?>
</div>
<script>
    const btn = document.getElementById('add-comment');

    btn.addEventListener('click', () => {
        const form = document.getElementById('comment-form');

        if (form.style.display === 'block') {
            form.style.display = 'none';
        } else {
            form.style.display = 'block';
        }
    });

function deleteComment(user, key) {
    var confirmation = confirm("Delete this comment?");
    if (confirmation){
        $.ajax({
            type: 'post',
            url: '/readers/delete-comment?ID=' + user + '&key=' + key,
            success: function(){
                $("#row" + key).remove();
            },
            error: function () {
                alert("Submission error.");
            }
        });
    }
};
</script>