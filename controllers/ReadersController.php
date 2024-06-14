<?php

namespace app\controllers;

use app\models\Readers;
use app\models\ReadersSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use app\models\LoginForm;
use Yii;

/**
 * ReadersController implements the CRUD actions for Readers model.
 */
class ReadersController extends Controller
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
                        'actions' => ['all-users', 'create', 'send-comment', 'suspend-user', 'unsuspend-user', 'delete-comment'],
                        'roles' => ['Librarian', 'Admin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['Admin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view', 'change-password',  'edit'],
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
     * Lists all users.
     *
     * @return string
     */
    public function actionAllUsers()
    {
        $searchModel = new ReadersSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('all-users', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Readers model.
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
     * Changes the current password.
     */
    public function actionChangePassword($ID)
    {
        $user = $this->findModel($ID);
        if ($this->request->isPost && $user->load($this->request->post()) && $user->validateCurrentPassword($user)){
            $user->NewPassword = Yii::$app->getSecurity()->generatePasswordHash($user->NewPassword.$user->ID);
            $user->Password = $user->NewPassword;
            $user->save(false);
            Yii::$app->session->setFlash('success', 'You have successfully changed your password!');
            return $this->render('edit', [
                'model' => $this->findModel($ID),
            ]);
        }

        return $this->render('change-password', [
            'model' => $this->findModel($ID),
        ]);
    }

    /**
     * Sends a comment for a specific user.
     */
    public function actionSendComment()
    {
        $comment = $_GET['txtcomment'];
        $sender_id = $_GET['id-sender'];
        $user_id = $_GET['id-user'];

        $user = Readers::findOne(['ID' => $user_id]);

        $comments = $user->Comments;

        if(empty($comments)){
            $comments = [];
        }
        else{
            $comments = \yii\helpers\Json::decode($comments);
        }

        $sender_comment[$sender_id] = $comment;
        array_push($comments, $sender_comment);
        $comments = \yii\helpers\Json::encode($comments);

        $user->Comments = $comments;

        $user->save(false);

        if ($_SESSION['__id'] == $user_id){
            Yii::$app->user->logout();
            $model = new LoginForm();
            return $this->render('login', [
                'model' => $model,
            ]);
        }

        return $this->redirect(['view', 'ID' => $user_id]);
    }

    /**
     * Deletes a comment.
     */
    public function actionDeleteComment($ID, $key)
    {
        $user = Readers::findOne(['ID' => $ID]);
        $comments = \yii\helpers\Json::decode($user->Comments);
        unset($comments[$key]);
        $user->Comments = $comments;
        $user->Comments = \yii\helpers\Json::encode($user->Comments);
        $user->save(false);
    }

    /**
     * Suspends a user.
     */
    public function actionSuspendUser($ID)
    {
        $user = Readers::findOne(['ID' => $ID]);
        $user->Active = FALSE;
        $user->save(false);

        return $this->redirect(['view', 'ID' => $user->ID]);
    }

    /**
     * Unsuspends a user.
     */
    public function actionUnsuspendUser($ID)
    {
        $user = Readers::findOne(['ID' => $ID]);
        $user->Active = TRUE;
        $user->save(false);

        return $this->redirect(['view', 'ID' => $user->ID]);
    }

    /**
     * Creates a new Readers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Readers();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {

                $image = UploadedFile::getInstance($model, 'ProfilePicture');

                $model->Password = Yii::$app->getSecurity()->generatePasswordHash($model->Password.$model->ID);

                $model->save(false);

                if ($image){

                    $model->ProfilePicture = $model->FirstName . '-' . $model->LastName . '-' . date('Y-m-d h:i:s');

                    if (!is_dir(Yii::$app->basePath . '/web/profile_pictures/' . $model->ID)) {
                        mkdir(Yii::$app->basePath . '/web/profile_pictures/' . $model->ID);
                    }

                    $image->saveAs(Yii::$app->basePath . '/web/profile_pictures/' . $model->ID . '/' . $model->FirstName . '-' . $model->LastName . '-' . date('Y-m-d h:i:s'));

                }

                $type = 'Reader';
                $model->FirstName = strtolower($model->FirstName);
                $model->LastName = strtolower($model->LastName);
                $model->FirstName = ucfirst($model->FirstName);
                $model->LastName = ucfirst($model->LastName);

                if(isset($_POST['Readers']['Type'])){
                    if ($_POST['Readers']['Type'] == 0){
                        $type = 'Reader';
                    }
                    else if ($_POST['Readers']['Type'] == 1){
                        $type = 'Librarian';
                    }
                    else {
                        $type = 'Admin';
                    }
                }

                $model->authKey = Yii::$app->security->generateRandomString();
                $auth = Yii::$app->authManager;
                $role = $auth->getRole($type);
                $auth->assign($role, $model->ID);
                $model->Type = $type;
                $model->save(false);

                return $this->goHome();
            }
        }
        else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Edits an existing Readers model.
     * If edit is successful, the browser will be redirected to the 'view' page.
     * @param string $ID ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionEdit($ID)
    {
        $model = $this->findModel($ID);

        if ($this->request->isPost && $model->load($this->request->post())) {
            $user = $this->findModel($ID);

            $picture = $user->ProfilePicture;

            $image = UploadedFile::getInstance($model, 'ProfilePicture');

            $type = $model->Type;

            $model->FirstName = strtolower($model->FirstName);
            $model->LastName = strtolower($model->LastName);
            $model->FirstName = ucfirst($model->FirstName);
            $model->LastName = ucfirst($model->LastName);

            if(isset($_POST['Readers']['Type'])){
                if ($_POST['Readers']['Type'] == 0){
                    $type = 'Reader';
                }
                else if ($_POST['Readers']['Type'] == 1){
                    $type = 'Librarian';
                }
                else {
                    $type = 'Admin';
                }
            }

            if (isset($type) && $user->Type != $type){
                $auth = Yii::$app->authManager;
                $auth->revokeAll($model->ID);
                $role = $auth->getRole($type);
                $auth->assign($role, $model->ID);
                $model->Type = $type;
            }

            if ($image){

                FileHelper::removeDirectory(Yii::$app->basePath . '/web/profile_pictures/' . $model->ID);

                $model->ProfilePicture = $model->FirstName . '-' . $model->LastName . '-' . date('Y-m-d h:i:s');

                $model->save(false);

                if (!is_dir(Yii::$app->basePath . '/web/profile_pictures/' . $model->ID)) {

                    mkdir(Yii::$app->basePath . '/web/profile_pictures/' . $model->ID);
                }

                $image->saveAs(Yii::$app->basePath . '/web/profile_pictures/' . $model->ID . '/' . $model->FirstName . '-' . $model->LastName . '-' . date('Y-m-d h:i:s'));

                return $this->redirect(['view', 'ID' => $model->ID]);

            }

            $model->ProfilePicture = $picture;

            $model->save(false);

            return $this->redirect(['view', 'ID' => $model->ID]);
        }

        return $this->render('edit', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Readers model.
     * If deletion is successful, the browser will be redirected to the 'all-users' page.
     * @param string $ID ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ID)
    {
        $model = $this->findModel($ID);

        if ($model->ProfilePicture){
            FileHelper::removeDirectory(Yii::$app->basePath . '/web/profile_pictures/' . $model->ID);
        }

        $auth = Yii::$app->authManager;
        $role = $auth->getRole($model->Type);
        $auth->revoke($role, $model->ID);

        $model->delete();

        return $this->redirect(['all-users']);
    }

    /**
     * Finds the Readers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $ID ID
     * @return Readers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ID)
    {
        if (($model = Readers::findOne(['ID' => $ID])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}