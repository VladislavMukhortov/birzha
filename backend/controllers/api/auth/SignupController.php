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
use app\models\notice\EmailNotification;

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
     * Регистрируем пользователя
     * @return string
     */
    public function actionIndex() : Response
    {
        $param = [
            'member-name' => trim(Yii::$app->request->post('member-name', '')),
            'member-phone' => trim(Yii::$app->request->post('member-phone', '')),
            'member-email' => trim(Yii::$app->request->post('member-email', '')),
            'company-name' => trim(Yii::$app->request->post('company-name', '')),
            'company-swift' => trim(Yii::$app->request->post('company-swift', '')),
            'company-iban' => trim(Yii::$app->request->post('company-iban', '')),
        ];

        $data = [
            'success' => false
        ];

        if (
            $param['member-name'] &&
            $param['member-phone'] &&
            $param['member-email'] &&
            $param['company-name'] &&
            $param['company-swift'] &&
            $param['company-iban']
        ) {
            $company = new Company();
            Company::getDb()->transaction(function($db) use ($company, $param) {
                $company->setName($param['company-name']);
                $company->setSWIFT($param['company-swift']);
                $company->setIBAN($param['company-iban']);
                $company->setVerifyEmail();
                $company->setVerifyPhone();
                $company->companyChangedData();
                $company->save();
            });

            if (!$company->hasErrors()) {
                // пароль для пользователя
                $password = User::generatePassword();
                // $password = '1234567q';

                $user = new User();
                User::getDb()->transaction(function($db) use ($user, $company, $param, $password) {
                    $user->setName($param['member-name']);
                    $user->generateLink();
                    $user->setEmail($param['member-email']);
                    $user->setPhone($param['member-phone']);
                    $user->company_id = $company->id;
                    $user->timezone = '+00:00';
                    $user->language = app()->language;
                    $user->setVerifyEmail();
                    $user->setVerifyPhone();
                    $user->setAccessToken();
                    $user->setPassword($password);
                    $user->save();
                });

                if (!$user->hasErrors()) {
                    $data['success'] = true;
                    // данные для авторизации не передаются так как авторизация проходит после подтверждения почты
                    $data['name'] = $user->name;
                    $data['email'] = $user->email;
                    $data['phone'] = $user->phone;

                    if (!EmailNotification::newUserRegistration($user, $password)) {
                        /**
                         * TODO: если сообщение не отправилось то надо что то сделать
                         * например записать его на более позднюю отправку
                         * или уведомить администратора
                         * или уведомить пользователя, что доставка писем не работает
                         * или удалить его аккаунт (но не компанию, вдруг в ней кто то есть)
                         */
                    }
                }
            }
        }

        return $this->asJson($data);
    }

}
