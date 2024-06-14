<?php

use app\models\Readers;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ReadersSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Users';
?>
<div class="readers-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create new user', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'Type',
            'FirstName',
            'LastName',
            'Email:email',
            'Phone',
            [
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a( Html::img(Yii::$app->request->baseUrl."/images/Profile.jpg", ['class' => 'profile-view']),
                    Url::to(['/readers/view', 'ID' => $model->ID]));
                },
            ],
            [
                'format'=>'raw',    
                'value' => function($model)
                {   
                    if(isset($_POST['Books']['BookID']))
                    {
                        return Html::a('Give book to user', ['/reserved-books/give-book-to-user', 'user_id' => $model->ID, 'book_id' => $_POST['Books']['BookID']], ['class' => 'btn btn-primary book-to-usr']);
                    }
                    else {
                        return Html::a('Create cart', ['/reserved-books/create-cart', 'user_id' => $model->ID], ['class' => 'btn btn-primary create-cart']);
                    }
                },
            ],

            //'Address',
            //'Comments:ntext',
            //'Active',
            //'ProfilePicture:ntext',
        ],
    ]); ?>


</div>