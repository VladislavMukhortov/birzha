<?php
declare(strict_types=1);

namespace app\controllers\api\user;

use Yii;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\web\Response;

use app\models\User;
use app\models\Company;

/**
 * API
 */
class ShowController extends Controller
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
     * Информация о пользователе по ID
     * @param  integer 'id' ID пользователя
     * @return string
     */
    public function actionIndex() : Response
    {
        $user = User::find()
            ->select('id, name, email, phone, position, company_id, created_at')
            ->where([
                'id' => (int) Yii::$app->request->get('id', 0),
                'status' => User::STATUS_ACTIVE
            ])
            ->asArray()
            ->one();

        if ($user) {
            $company = Company::find()
                ->select('name, location, swift, iban, bank_name, bank_location')
                ->where([
                    'id' => (int) $user['company_id']
                ])
                ->asArray()
                ->one();

            unset($user['company_id']);

            if ($company) {
                $user['company'] = [
                    'name' => $company['name'],
                    'location' => $company['location'],
                    'swift' => $company['swift'],
                    'iban' => $company['iban'],
                    'bank_name' => $company['bank_name'],
                    'bank_location' => $company['bank_location'],
                ];
            }

        } else {
            $user = [
                'error' => 'User not found'
            ];
        }

        return $this->asJson($user);
    }



}
