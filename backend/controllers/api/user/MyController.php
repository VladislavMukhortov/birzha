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

use app\models\DataChange;

/**
 * API Личные данные пользователя
 */
class MyController extends Controller
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
     * @return string
     */
    public function actionIndex() : Response
    {
        $user = Yii::$app->user->identity;

        // время изменения пароля
        $change_password_at = DataChange::find()
            ->select('created_at')
            ->where([
                'user_id' => $user->id,
                'key' => DataChange::KEYS['user_password'],
            ])
            ->orderBy('id DESC')
            ->scalar();

        if (!$change_password_at) {
            $change_password_at = $user->created_at;
        }

        $data = [
            'id' => (int) $user->id,
            'name' => strval($user->name),
            'email' => strval($user->email),
            'phone' => strval($user->phone),
            'position' => strval($user->position),
            'verify_email' => (boolean) ! $user->verify_email,
            'verify_phone' => (boolean) ! $user->verify_phone,
            'change_password_at' => strval($change_password_at),
        ];

        return $this->asJson($data);
    }



}
