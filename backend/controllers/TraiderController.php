<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

use app\models\User;

/**
 *
 */
class TraiderController extends Controller
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
                    'index' => ['GET'],
                    'my' => ['GET'],
                ]
            ],
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    return $this->goHome();
                },
                'only' => ['my'],
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
     * Страница пользователя
     * @param  int    $link url пользователя
     * @return string
     */
    public function actionIndex(string $link) : string
    {
        $user = User::find()
            ->where([
                'link' => $link,
                'status' => User::STATUS_ACTIVE
            ])
            ->one();

        if (!$user) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        return $this->render('index', [
            'user' => $user
        ]);
    }



    /**
     * Личный профиль
     * @return string
     */
    public function actionMy() : string
    {
        return $this->render('my', [
            'user' => user()->identity
        ]);
    }

}
