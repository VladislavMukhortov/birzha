<?php
declare(strict_types=1);

namespace app\controllers\api\company;

use Yii;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\web\Response;

use yii\db\Query;

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
        $trader_id = (int) Yii::$app->request->get('id', 0);

        $trader = (new Query())
            ->select('id, name, location, swift, iban, bank_name, bank_location, email, phone, site, director, text')
            ->from(Company::tableName())
            ->where([
                'id' => $trader_id,
                'status' => Company::STATUS_ACTIVE
            ])
            ->one();

        if ($trader) {
            $trader['name'] = strval($trader['name']);
            $trader['location'] = strval($trader['location']);
            $trader['swift'] = strval($trader['swift']);
            $trader['iban'] = strval($trader['iban']);
            $trader['bank_name'] = strval($trader['bank_name']);
            $trader['bank_location'] = strval($trader['bank_location']);
            $trader['email'] = strval($trader['email']);
            $trader['phone'] = strval($trader['phone']);
            $trader['site'] = strval($trader['site']);
            $trader['director'] = strval($trader['director']);
            $trader['text'] = strval($trader['text']);
        } else {
            $trader = [
                'error' => 'Trader not found'
            ];
        }

        return $this->asJson($trader);
    }



}
