<?php

namespace app\controllers;

use app\models\RentedBooks;
use app\models\RentedBooksSearch;
use app\models\ReservedBooks;
use app\models\Books;
use app\models\Readers;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use Yii;

/**
 * RentedBooksController implements the CRUD actions for RentedBooks model.
 */
class RentedBooksController extends Controller
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
                        'actions' => ['approve-order', 'update', 'return-book'],
                        'roles' => ['Librarian', 'Admin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['taken-books', 'book-history'],
                        'roles' => ['@'],
                    ]
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
     * Updates an existing RentedBooks model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $ID ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($ID)
    {
        $model = $this->findModel($ID);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ID' => $model->ID]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the RentedBooks model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $ID ID
     * @return RentedBooks the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ID)
    {
        if (($model = RentedBooks::findOne(['ID' => $ID])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Lists all taken books by the user.
     *
     * @return string
     */
    public function actionTakenBooks($userID)
    {
        $searchModel = new RentedBooksSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('taken-books', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'user_id' => $userID
        ]);
    }

    /**
     * Lists book history for a specific user.
     *
     * @return string
     */
    public function actionBookHistory($userID)
    {
        $searchModel = new RentedBooksSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('book-history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'user_id' => $userID
        ]);
    }

    /**
     * The reserved books are being approved by the librarian and given to the reader.
     *
     */
    public function actionApproveOrder($ID)
    {
        $usr_id = $ID;
        $user = Readers::findOne(['ID' => $usr_id]);
        $books = ReservedBooks::findAll(['ReaderID' => $user->ID]);
        foreach ($books as $index=>$data){
            $new_model = new RentedBooks();
            $new_model->BookID = $books[$index]['BookID'];
            $new_model->ReaderID = $books[$index]['ReaderID'];
            $new_model->TakenDate = date('Y-m-d H:i:s');
            $new_model->ReturnDate = date('Y-m-d H:i:s', strtotime('+30 days'));
            $new_model->Amount = $books[$index]['Amount'];
            $new_model->save();
            $id = $books[$index]['ID'];
            $model = ReservedBooks::findOne(['ID' => $id]);
            $model->delete();
            $book = Books::findOne(['ID' => $books[$index]['BookID']]);
            $book->CurrentAmount -= $books[$index]['Amount'];
            $book->save(false);
        }
        unset($_SESSION['reserved_usr_id']);
        return $this->redirect(['taken-books?userID=' . $usr_id]);
    }

    /**
     * Return a rented book.
     *
     */
    public function actionReturnBook($id)
    {
        $model = $this->findModel($id);

        $model->ReturnDate = NULL;

        $model->ReturnedDate = date('Y-m-d H:i:s');

        $book = Books::findOne(['ID' => $model->BookID]);

        $book->CurrentAmount += $model->Amount;

        $book->save();

        $model->save();
    }
}