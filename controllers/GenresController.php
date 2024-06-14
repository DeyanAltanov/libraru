<?php

namespace app\controllers;

use app\models\Genres;
use app\models\GenresSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use Yii;

/**
 * GenresController implements the CRUD actions for Genres model.
 */
class GenresController extends Controller
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
                        'actions' => ['all-genres', 'create', 'update','delete'],
                        'roles' => ['Librarian', 'Admin'],
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
     * Lists all Genres models.
     *
     * @return string
     */
    public function actionAllGenres()
    {
        $searchModel = new GenresSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('all-genres', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Genres model.
     * If creation is successful, the browser will be redirected to the 'all-genres' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Genres();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if (Genres::find()->where(['Genre' => $_POST['Genres']['Genre']])->all()){
                    Yii::$app->session->setFlash('error', 'This genre already exists!');
                    return $this->render('create', ['model' => $model,]);
                }
                $model->save();
                return $this->redirect(['all-genres']);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Genres model.
     * If update is successful, the browser will be redirected to the 'all-genres' page.
     * @param string $ID ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($ID)
    {
        $model = $this->findModel($ID);

        if ($this->request->isPost && $model->load($this->request->post())) {
            if (Genres::find()->where(['Genre' => $_POST['Genres']['Genre']])->one()){
                Yii::$app->session->setFlash('error', 'This genre already exists!');
                return $this->render('create', ['model' => $model,]);
            }
            $model->save();
            return $this->redirect(['all-genres']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Genres model.
     * If deletion is successful, the browser will be redirected to the 'all-genres' page.
     * @param string $ID ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ID)
    {
        $this->findModel($ID)->delete();

        return $this->redirect(['all-genres']);
    }

    /**
     * Finds the Genres model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $ID ID
     * @return Genres the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ID)
    {
        if (($model = Genres::findOne(['ID' => $ID])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}