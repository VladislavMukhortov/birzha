<?php

declare(strict_types=1);

namespace app\controllers\auth;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use app\models\form\SigninForm;

/**
 * Авторизация пользователя
 */
class SigninController extends Controller
{

    /**
     * @return array
     */
    public function behaviors() : array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET', 'POST']
                ]
            ],
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    return $this->goHome();
                },
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?']
                    ]
                ]
            ]
        ];
    }



    /**
     * Авторизация пользователя
     */
    public function actionIndex()
    {
        if (!user()->isGuest) {
            return $this->goHome();
        }

        $model = new SigninForm();
        if ($model->load(request()->post()) && $model->login()) {
            return $this->goBack();
        }

        // $model->password = '';
        return $this->render('index', [
            'model' => $model
        ]);
    }

}
