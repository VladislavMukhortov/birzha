<?php

declare(strict_types=1);

namespace app\controllers\api\auth;

use Yii;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\web\Response;

use app\models\User;
use app\models\Company;

/**
 * Восстановление пароля
 */
class PasswordResetController extends Controller
{

    /**
     * @return array
     */
    public function behaviors() : array
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'corsFilter' => [
                'class' => Cors::className(),
                'cors' => [
                    'Origin' => app()->params['cors.origin'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age' => 3600
                ],
            ],

            'verbFilter' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET', 'POST', 'OPTIONS']
                ]
            ]
        ]);
    }



    /**
     * @inheritdoc
     */
    public function actions() : array
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction'
            ],
        ];
    }



    /**
     * Проверка возможности изменения пароля по токену
     * Отдаем пользователю токен АПИ, что бы он сменил пароль
     * @return string
     */
    public function actionIndex() : Response
    {
        $token = trim(Yii::$app->request->get('token', ''));

        $data = [
            'success' => false
        ];

        if (!empty($token) && is_string($token)) {

            $user = User::findIdentityByPasswordResetToken($token);
            if ($user) {
                $data['success'] = true;
                $data['name'] = $user->name;
                $data['email'] = $user->email;
                $data['phone'] = $user->phone;
                $data['company'] = Company::find($user->company_id)
                    ->select('name')
                    ->scalar();
                $data['access_token'] = $user->access_token;
            }

            /**
             * TODO: записывать логи
             */
        }

        return $this->asJson($data);
    }

}
