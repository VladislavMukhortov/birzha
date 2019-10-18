<?php
declare(strict_types=1);

namespace app\controllers\api\user;

use Yii;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpHeaderAuth;
use yii\web\Response;

use app\models\User;
use app\models\DataChange;
use app\models\notice\EmailNotification;

/**
 * API Изменение пароля
 */
class ChangePasswordController extends Controller
{

    /**
     * {@inheritdoc}
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
            ],

            'authenticator' => [
                'class' => HttpHeaderAuth::className(),
                'except' => ['options']
            ]
        ]);
    }



    /**
     * {@inheritdoc}
     */
    public function beforeAction($action) : bool
    {
        Yii::$app->user->enableSession = false;
        return parent::beforeAction($action);
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
     * Меняем вароль пользователя и АПИ-токен
     * записываем за пользователем изменение пароля
     * и отправляем новый пароль ему на почту
     * @return string
     */
    public function actionIndex() : Response
    {
        $password = trim(Yii::$app->request->post('password', ''));
        $confirm = trim(Yii::$app->request->post('confirm', ''));

        $output = [
            'result' => 'error',
        ];

        if (strlen($password) < User::PSSW_LENGTH_MIN) {
            return $this->asJson($output);
        }

        if ($password !== $confirm) {
            return $this->asJson($output);
        }

        $user = User::findIdentity(Yii::$app->user->identity->id);
        if ($user) {
            $user_password_hash = Yii::$app->user->identity->password_hash;

            User::getDb()->transaction(function($db) use ($user, $password) {
                // меняем пароль
                $user->setPassword($password);
                // меняем токен
                $user->setAccessToken();
                // удаляем ссылку для восстановления пароля
                $user->removePasswordResetToken();
                $user->save(false);
            });

            if (!$user->hasErrors()) {
                $output['result'] = 'success';
                $output['access_token'] = $user->access_token;
                DataChange::add(DataChange::KEYS['user_password'], $user_password_hash);
                EmailNotification::userChangePassword($user, $password);
            }

            /**
             * TODO: логировать операцию
             */
        }

        return $this->asJson($output);
    }



}
