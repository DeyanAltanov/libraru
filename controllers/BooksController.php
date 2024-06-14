<?php

namespace app\controllers;

use app\models\Books;
use app\models\Genres;
use yii\filters\AccessControl;
use app\models\BookGenres;
use app\models\BooksSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use Yii;

/**
 * BooksController implements the CRUD actions for Books model.
 */
class BooksController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['rented-by', 'overdue-books', 'create', 'edit', 'find-by-isbn', 'delete-book-image', 'delete'],
                        'roles' => ['Librarian', 'Admin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Displays a single Books model.
     * @param string $ID ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ID)
    {
        return $this->render('view', [
            'model' => $this->findModel($ID),
        ]);
    }

    /**
     * Shows all users who has rented the selected book.
     */
    public function actionRentedBy($ID)
    {
        return $this->render('rented-by', [
            'model' => $this->findModel($ID),
        ]);
    }

    /**
     * Lists of all overdue books.
     */
    public function actionOverdueBooks()
    {
        return $this->render('overdue-books');
    }

    /**
     * Creates a new Books model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Books();

        $genres = new Genres();

        $genres = $genres->find()->all();

        if ($this->request->isPost) {
            if($model->load(Yii::$app->request->post())){
                $images = UploadedFile::getInstances($model, 'Images');

                $model->Title = ucwords(strtolower($model->Title));
                $model->Author = ucwords(strtolower($model->Author));

                $all_images = [];

                $model->CurrentAmount = $model->TotalAmount;

                $model->Images = \yii\helpers\Json::encode($all_images);

                $model->save();

                if (isset($_POST['genres'])){
                    $genres = array_unique($_POST['genres']);

                    foreach ($genres as $index=>$genreID){
                        $book_genres = new BookGenres();
                        $book_genres->BookID = $model->ID;
                        $book_genres->GenreID = $genreID;
                        $book_genres->save();
                    }
                }

                mkdir(Yii::$app->basePath . '/web/upload/' . $model->ID);

                if($images){
                    $all_images = \yii\helpers\Json::decode($model->Images);
                    $i = 1;
                    foreach($images as $image){
                        array_push($all_images, str_replace(' ', '', $model->Title . '-' . date('Y-m-d h:i:s') . $i));
                        $image->saveAs(Yii::$app->basePath . '/web/upload/' . $model->ID . '/' . str_replace(' ', '', $model->Title . '-' . date('Y-m-d h:i:s') . $i));
                        $i++;
                    }
                }

                $model->Images = \yii\helpers\Json::encode($all_images);

                $model->save();

                return $this->redirect(['view', 'ID' => $model->ID]);
            }
        }
        else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'genres' => $genres,
        ]);
    }

    /**
     * Edits an existing Books model.
     * If edit is successful, the browser will be redirected to the 'view' page.
     * @param string $ID ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionEdit($ID)
    {
        $model = $this->findModel($ID);

        $model_images = $model->Images;

        $genres = new Genres();

        $genres = $genres->find()->all();

        $current_total_amount = $model->TotalAmount;

        if ($this->request->isPost && $model->load($this->request->post())) {
            $images = UploadedFile::getInstances($model, 'Images');

            $model->Title = ucwords(strtolower($model->Title));
            $model->Author = ucwords(strtolower($model->Author));

            if(empty($model_images)){
                $model_images = [];
            }
            else{
                $model_images = \yii\helpers\Json::decode($model_images);
            }

            if ($images) {
                if (!is_dir(Yii::$app->basePath . '/web/upload/' . $model->ID)) {
                    mkdir(Yii::$app->basePath . '/web/upload/' . $model->ID);
                }

                $i = 1;
                foreach($images as $image){
                    array_push($model_images, str_replace(' ', '', $model->Title . '-' . date('Y-m-d h:i:s') . $i));
                    $image->saveAs(Yii::$app->basePath . '/web/upload/' . $model->ID . '/' . str_replace(' ', '', $model->Title . '-' . date('Y-m-d h:i:s') . $i));
                    $i++;
                }
            }

            if(isset($_POST['CurrentImages'])){
                $model_images = $_POST['CurrentImages'];
            }

            $model_images = \yii\helpers\Json::encode($model_images);

            $model->Images = $model_images;

            if (isset($_POST['genres'])){
                $model->ISBN = $_POST['Books']['ISBN'];
                $model->save();
                $bookID = $this->findByIsbn($_POST['Books']['ISBN']);
                $genres = $_POST['genres'];
                $books = BookGenres::findAll(['BookID' => $bookID]);

                $genres_book = [];
                foreach($books as $book){
                    array_push($genres_book, $book->GenreID);
                }

                foreach ($genres as $index=>$genreID){
                    if (!in_array($genreID, $genres_book)){
                        $book_genres = new BookGenres();
                        $book_genres->BookID = $bookID;
                        $book_genres->GenreID = $genreID;
                        $book_genres->save();
                    }
                }

                unset ($genres_book);
            }

            if ($current_total_amount != $_POST['Books']['TotalAmount']){
                if ($_POST['Books']['TotalAmount'] > $current_total_amount){
                    $model->CurrentAmount += abs($_POST['Books']['TotalAmount'] - $current_total_amount);
                }
                else{
                    $model->CurrentAmount -= abs($_POST['Books']['TotalAmount'] - $current_total_amount);
                }
            }

            $model->save();

            return $this->redirect(['view', 'ID' => $model->ID]);
        }

        return $this->render('edit', [
            'model' => $model,
            'genres' => $genres,
        ]);
    }

    /**
     * Finds book by ISBN.
     */
    public static function findByIsbn($isbn)
    {
        if (($model = Books::findOne(['ISBN' => $isbn])) !== null) {
            return $model->ID;
        }

        throw new NotFoundHttpException('The requested book does not exist.');
    }

    /**
     * Delete an image from a book.
     *
     */
    public function actionDeleteBookImage($id, $img)
    {
        $model = Books::findOne(['ID' => $id]);

        $model_images = \yii\helpers\Json::decode($model->Images);

        foreach($model_images as $key => $value){
            if ($value == $img){
                unlink(Yii::$app->basePath . '/web/upload/' . $model->ID . '/' . $value);
                unset($model_images[$key]);
                $model->Images = \yii\helpers\Json::encode($model_images);
                break;
            }
        }

        $model->save();
    }

    /**
     * Deletes an existing Books model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $ID ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ID)
    {
        $model = $this->findModel($ID);

        if ($model->Images != '[]'){
            $model_images = \yii\helpers\Json::decode($model->Images);

            foreach($model_images as $image){
                unlink(Yii::$app->basePath . '/web/upload/' . $model->ID . '/' . $image);
            }
 
        }

        rmdir(Yii::$app->basePath . '/web/upload/' . $model->ID);

        $model->delete();

        return $this->redirect(['/site/catalogue']);
    }

    /**
     * Finds the Books model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $ID ID
     * @return Books the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ID)
    {
        if (($model = Books::findOne(['ID' => $ID])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}