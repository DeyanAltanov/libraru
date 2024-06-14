<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\SignupForm;
use app\models\Books;
use app\models\Readers;
use app\models\Genres;
use app\models\BookGenres;
use app\models\BooksSearch;
use yii\helpers\Json;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     */
    public function actionCatalogue()
    {
        $books = Books::find();
 
        $totalCount = $books->count();
        $pagination = new Pagination([
            'defaultPageSize' => 25,
            'totalCount' => $totalCount,
        ]);

        $books = $books->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('catalogue', [
            'books'=>$books,
            'pagination'=>$pagination
        ]);
    }

    /**
     * Catalogue search.
     *
     */
    public function actionSearchBooks()
    {
        $books = Books::find();

        if(isset($_GET['BooksSearch']['globalSearch'])){
            $search_params = $_GET['BooksSearch']['globalSearch'];
            $books = Books::find()
                                ->where(['Like', 'Title', strtolower($search_params)])
                                ->orWhere(['Like', 'Author', strtolower($search_params)])
                                ->orWhere(['Like', 'ISBN', strtolower($search_params)]);
            if (isset($_GET['genres'])) {
                $genreIDs = $_GET['genres'];
                $bookGenresSubquery = BookGenres::find()->select('BookID')->where(['IN', 'GenreID', $genreIDs]);
                $books->andWhere(['IN', 'ID', $bookGenresSubquery]);
            }
        }
 
        $totalCount = $books->count();
        $pagination = new Pagination([
            'defaultPageSize' => 25,
            'totalCount' => $totalCount,
        ]);

        $books = $books->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('catalogue', [
            'books'=>$books,
            'pagination'=>$pagination
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goHome();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Signup action.
     *
     * @return Response|string
     */
    public function actionSignup()
    {
        $model = new Readers();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {

                $image = UploadedFile::getInstance($model, 'ProfilePicture');

                $model->save(false);

                $model->Password = Yii::$app->getSecurity()->generatePasswordHash($model->Password.$model->ID);

                if ($image){

                    $model->ProfilePicture = $model->FirstName . '-' . $model->LastName . '-' . date('Y-m-d h:i:s');

                    if (!is_dir(Yii::$app->basePath . '/web/profile_pictures/' . $model->ID)) {
                        mkdir(Yii::$app->basePath . '/web/profile_pictures/' . $model->ID);
                    }

                    $image->saveAs(Yii::$app->basePath . '/web/profile_pictures/' . $model->ID . '/' . $model->FirstName . '-' . $model->LastName . '-' . date('Y-m-d h:i:s'));

                }

                $model->FirstName = strtolower($model->FirstName);
                $model->LastName = strtolower($model->LastName);
                $model->FirstName = ucfirst($model->FirstName);
                $model->LastName = ucfirst($model->LastName);
                $model->Type = 'Reader';

                $model->authKey = Yii::$app->security->generateRandomString();
                $auth = Yii::$app->authManager;
                $role = $auth->getRole($model->Type);
                $auth->assign($role, $model->ID);
                $model->save(false);

                return $this->goHome();
            }
        }
        else {
            $model->loadDefaultValues();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        $books = Books::find()->all();
        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     */
    public function actionContact()
    {
        return $this->render('contact');
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Displays a single Books model.
     * @param string $ID ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ID)
    {
        return $this->render('/books/view', [
            'model' => $this->findModel($ID),
        ]);
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