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

/**
 * Проверка данных на уникальность
 */
class IsUniqueDataController extends Controller
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
     * Проверка данных на уникальность
     * @return string
     */
    public function actionIndex() : Response
    {
        $phone = trim(Yii::$app->request->post('phone', ''));
        $email = trim(Yii::$app->request->post('email', ''));

        $output = [
            'result' => 'success',
            'phone' => true,
            'email' => true,
        ];

        if ($phone) {
            $output['phone'] = !User::find()
                ->where([
                    'phone' => User::cleanPhoneNumber($phone)
                ])
                ->exists();
        }

        if ($email) {
            $output['email'] = !User::find()
                ->where([
                    'email' => User::cleanEmail($email)
                ])
                ->exists();
        }

        return $this->asJson($output);
    }

}
