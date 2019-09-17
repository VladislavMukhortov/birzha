<?php

declare(strict_types=1);

namespace app\controllers\auth;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use app\models\form\SignupForm;
use app\models\Country;

/**
 * Регистрация пользователя
 */
class SignupController extends Controller
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
     * Регистрация пользователя
     */
    public function actionIndex()
    {
        $model = new SignupForm;

        if ($model->load(request()->post())) {
            $user = $model->signup();

            if (null !== $user && user()->login($user)) {
                return $this->goBack();
            }
        }

        $country = Country::find()
            ->select('name')
            ->where(['status' => Country::STATUS_ACTIVE])
            ->orderBy('sort ASC')
            ->indexBy('iso_code_2')
            ->column();

        return $this->render('index', [
            'model' => $model,
            'country' => $country
        ]);
    }

}
