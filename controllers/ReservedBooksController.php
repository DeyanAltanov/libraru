<?php

namespace app\controllers;

use app\models\ReservedBooks;
use app\models\ReservedBooksSearch;
use app\models\Readers;
use app\models\Books;
use yii\web\Controller;
use yii\web\Session;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\filters\AccessControl;

use Yii;

/**
 * ReservedBooksController implements the CRUD actions for ReservedBooks model.
 */
class ReservedBooksController extends Controller
{
    public $enableCsrfValidation = false;
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
                        'actions' => ['active-orders', 'give-book-to-user', 'create-cart'],
                        'roles' => ['Librarian', 'Admin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['submit-amount', 'clear-book-session', 'delete-reserved-book', 'current-order', 'submit-order', 'delete-order', 'cancel-order', 'reserved-books-user'],
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
     * Lists all ReservedBooks models.
     *
     * @return string
     */
    public function actionActiveOrders()
    {
        $searchModel = new ReservedBooksSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('active-orders', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Submit book amount.
     *
     */
    public function actionSubmitAmount()
    {
        if (!isset($_SESSION['usr_id'])){
            Yii::$app->session->set('usr_id', $_SESSION['__id']);
        }

        if (isset($_POST['Books']['Amount'])){
            $book_data = Yii::$app->request->post("Books");
            $reader = $_SESSION['usr_id'];
            $book_id = $book_data['BookID'];
            $book_amount = $book_data['Amount'];
        }

        if (!isset($_SESSION['order'])){
            Yii::$app->session->set('order', []);
        }

        foreach (Yii::$app->session->get('order') as $key=>$value) {
            foreach ($value as $id=>$amount) {
                if ($book_id == $id){
                    $_SESSION['order'][$key][$id] += $book_amount;
                    $book = Books::findOne(['ID' => $id]);
                    if ($_SESSION['order'][$key][$id] > $book->CurrentAmount){
                        Yii::$app->session->setFlash('error', "You cannot order more, than what's currently available!");
                        $_SESSION['order'][$key][$id] = $book->CurrentAmount;
                    }
                    return $this->redirect('current-order');
                }
            }
        }

        $order = Yii::$app->session->get('order');
        $book = [
            $book_id => $book_amount
        ];
        array_push($order, $book);
        Yii::$app->session->set('order', $order);
        return $this->redirect('current-order');
    }

    /**
     * Gives the selected book, to the selected user.
     *
     */
    public function actionGiveBookToUser($user_id, $book_id)
    {
        $user = Readers::findOne(['ID' => $user_id]);
        if($user->Active == false){
            Yii::$app->session->setFlash('error', 'This user is banned!');
            return $this->redirect(['readers/all-users']);
        }

        if (!isset($_SESSION['usr_id'])){
            Yii::$app->session->set('usr_id', $user_id);
        }
        else if ($_SESSION['usr_id'] != $user_id){
            Yii::$app->session->setFlash('error', "There's already an active order!");
            return $this->redirect(['readers/all-users']);
        }

        if (!isset($_SESSION['order'])){
            Yii::$app->session->set('order', []);
        }

        $order = Yii::$app->session->get('order');
        $book = [
            $book_id => 1
        ];
        array_push($order, $book);
        Yii::$app->session->set('order', $order);
        return $this->redirect('current-order');
    }

    /**
     * Creates a cart, for the selected user.
     *
     */
    public function actionCreateCart($user_id)
    {
        if (!isset($_SESSION['usr_id']) && $user_id != $_SESSION['__id']){
            $user = Readers::findOne(['ID' => $user_id]);
            if($user->Active == false){
                Yii::$app->session->setFlash('error', 'This user is banned!');
                return $this->redirect(['readers/all-users']);
            }
            Yii::$app->session->set('usr_id', $user_id);
        }
        else {
            Yii::$app->session->setFlash('error', "There's already an active order!");
            return $this->redirect(['readers/all-users']);
        }

        return $this->redirect('current-order');
    }

    /**
     * Clears book from session.
     *
     */
    public function actionClearBookSession($key)
    {
        unset($_SESSION['order'][$key]);
        if (empty($_SESSION['order'])){
            unset($_SESSION['order']);
            unset($_SESSION['usr_id']);
        }
    }

    /**
     * Clears book from reserved user books.
     *
     */
    public function actionDeleteReservedBook($id)
    {
        $model = $this->findModel($id);

        $model->delete();
    }

    /**
     * Shows the current order.
     *
     */
    public function actionCurrentOrder()
    {
        return $this->render('current-order');
    }

    /**
     * Submission of the current order.
     */
    public function actionSubmitOrder()
    {
        $order = Yii::$app->session->get('order');
        $usr_id = $_SESSION['usr_id'];

        if ($this->request->isPost) {
            foreach ($order as $key=>$value) {
                foreach ($value as $id=>$amount) {
                    $model = new ReservedBooks();
                    $model->BookID = $id;
                    $model->ReaderID = $_SESSION['usr_id'];
                    $model->Date = date('Y-m-d H:i:s');
                    $model->Amount = $amount;
                    $model->save();
                    $book = Books::findOne(['ID' => $id]);
                    $book->CurrentAmount -= $amount;
                    $book->save();
                }
            }

            unset($_SESSION['order']);
            unset($_SESSION['usr_id']);
            $reserved = Yii::$app->session->set('reserved_usr_id', $usr_id);

            $this->redirect(['reserved-books-user?userID=' . $usr_id]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Deletion of the current order.
     */
    public function actionDeleteOrder()
    {
        if ($this->request->isPost) {
            unset($_SESSION['order']);
            unset($_SESSION['usr_id']);
            return $this->redirect('current-order');
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Cancelation of an active order.
     */
    public function actionCancelOrder($ID)
    {
        $userID = $ID;
        if ($this->request->isPost) {
            $reserved_orders = ReservedBooks::findAll(['ReaderID' => $userID]);
            foreach ($reserved_orders as $order){
                $order->delete();
            }
            unset($_SESSION['order']);
            unset($_SESSION['reserved_usr_id']);
            return $this->redirect(['reserved-books-user?userID=' . $userID]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the ReservedBooks model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $ID ID
     * @return ReservedBooks the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ID)
    {
        if (($model = ReservedBooks::findOne(['ID' => $ID])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Reserved books for each user.
     *
     */
    public function actionReservedBooksUser($userID)
    {
        $books = ReservedBooks::findAll(['ReaderID' => $userID]);
        return $this->render('reserved-books-user', [
            'user' => $userID,
            'books' => $books
        ]);
    }
}