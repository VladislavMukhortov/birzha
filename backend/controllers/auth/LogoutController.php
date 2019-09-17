<?php

declare(strict_types=1);

namespace app\controllers\auth;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use yii\web\Response;

/**
 * Выход пользователя из системы
 */
class LogoutController extends Controller
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
                        'roles' => ['@']
                    ]
                ]
            ]
        ];
    }



    /**
     * @return string
     */
    public function actionIndex() : Response
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

}
