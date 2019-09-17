<?php
declare(strict_types=1);

namespace app\controllers\api\company;

use Yii;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpHeaderAuth;
use yii\web\Response;

use app\models\Company;

/**
 * API Личные данные трейдера
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

        $company = Company::findOne($user->company_id);

        if ($company) {
            $data = [
                'success' => true,

                'id' => (int) $company->id,
                'name' => strval($company->name),
                'location' => strval($company->location),
                'swift' => strval($company->swift),
                'iban' => strval($company->iban),
                'bank_name' => strval($company->bank_name),
                'bank_location' => strval($company->bank_location),
                'email' => strval($company->email),
                'phone' => strval($company->phone),
                'site' => strval($company->site),
                'director' => strval($company->director),
                'text' => strval($company->text),
                'is_verify' => (boolean) $company->is_verify,
                'verify_email' => (boolean) ! $company->verify_email,
                'verify_phone' => (boolean) ! $company->verify_phone
            ];
        } else {
            $company = [
                'success' => false,
                'error' => 'Company not found'
            ];
        }

        return $this->asJson($data);
    }



}
