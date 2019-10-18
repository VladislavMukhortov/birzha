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
use app\models\notice\EmailNotification;

/**
 * Запрос на восстановление пароля
 * получение ссылки для восстановления пароля
 */
class RequestPasswordResetController extends Controller
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
     * Запрос на востановление пароля пользователя
     * Генерируем токен и отправляем его пользователю
     * @return string
     */
    public function actionIndex() : Response
    {
        $email = trim(Yii::$app->request->post('email', ''));
        $email = strtolower($email);

        $output = [
            'result' => 'error',
            'send' => false,    // отправлено ли уведомление
        ];

        $user = User::findIdentityByEmail($email);
        if ($user) {
            /**
             * TODO: логировать обращение пользователя на сброс пароля
             */

            $output['result'] = 'success';

            if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
                $user->generatePasswordResetToken();
                $user->save(false);

                if (!$user->hasErrors()) {
                    if (EmailNotification::requestPasswordReset($user)) {
                        $output['send'] = true;
                    }
                }
            }
        }

        return $this->asJson($output);
    }

}
