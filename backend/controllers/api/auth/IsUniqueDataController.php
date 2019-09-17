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
        $member_phone = trim(Yii::$app->request->post('member-phone', ''));
        $member_email = trim(Yii::$app->request->post('member-email', ''));

        $data = [
            'member_phone' => true,
            'member_email' => true
        ];

        if ($member_phone) {
            $member_phone = mb_convert_encoding(strval($member_phone), 'UTF-8', 'UTF-8');
            $member_phone = mb_substr($member_phone, 0, User::PHONE_LENGTH_MAX);
            $member_phone = strtolower($member_phone);
            $member_phone = preg_replace('/[^0-9\+]/', '', $member_phone);

            $data['member_phone'] = !User::find()->where(['phone' => $member_phone])->exists();
        }

        if ($member_email) {
            $member_email = mb_convert_encoding(strval($member_email), 'UTF-8', 'UTF-8');
            $member_email = mb_substr($member_email, 0, User::EMAIL_LENGTH_MAX);
            $member_email = strtolower($member_email);
            $member_email = preg_replace('/[^a-z0-9\.\@\_\-]/', '', $member_email);

            $data['member_email'] = !User::find()->where(['email' => $member_email])->exists();
        }

        return $this->asJson($data);
    }

}
