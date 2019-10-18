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
 * Подтверждение почты
 */
class VerifyEmailController extends Controller
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
     * Подтверждаем почтовый ящик
     * @return string
     */
    public function actionIndex() : Response
    {
        $token = trim(Yii::$app->request->get('token', ''));

        $output = [
            'result' => 'error',
        ];

        if ($token) {

            $user = User::findIdentityByVerifyEmailToken($token);
            if ($user) {

                $user->verify_email = NULL;

                $transaction = Yii::$app->db->beginTransaction();

                try {

                    if ($user->save()) {
                        $output['result'] = 'success';
                        $output['name'] = $user->name;
                        $output['email'] = $user->email;
                        $output['phone'] = $user->phone;
                        $output['company'] = Company::find($user->company_id)->select('name')->scalar();
                        $output['access_token'] = $user->access_token;
                    }

                    $transaction->commit();

                } catch(\Throwable $e) {
                    $output['error'] = 'Возникла ошибка, попробуйте посже';
                    $transaction->rollBack();
                    throw $e;
                }
            }

            /**
             * TODO: записывать логи
             */
        }

        return $this->asJson($output);
    }

}
