<?php
declare(strict_types=1);

namespace app\controllers\api\offer;

use Yii;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpHeaderAuth;
use yii\web\Response;

use app\models\form\offer\NewPrice;
use app\models\form\offer\AcceptPrice;
use app\models\form\offer\CancelPrice;

/**
 * API Создание оффера
 */
class UpdateController extends Controller
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



    public function actionIndex() : Response
    {
        return $this->asJson(true);
    }



    /**
     * Торг цены в оффере
     *
     * В статусе ТВЕРДО вторая сторона может 3 раза предложить свою цену.
     * Владелец либо соглашается на цену либо предлагает свою.
     * Когда вторая сторона предлагает владельце цену в третий раз
     * то у владелец модежет либо отказать либо принять.
     *
     * @param  string 'link'  url оффера
     * @param  float  'price' новая предлагаемая цена
     * @return string
     */
    public function actionNewPrice() : Response
    {
        $result = [
            'result' => 'error',
        ];

        $model = new NewPrice();
        if ($model->load(Yii::$app->request->post(), '')) {
            $result = $model->save();
        }

        return $this->asJson($result);
    }



    /**
     * Принимаем цену которую торговали в твердо
     * @return string
     */
    public function actionAcceptPrice() : Response
    {
        $result = [
            'result' => 'error',
        ];

        $model = new AcceptPrice();
        if ($model->load(Yii::$app->request->post(), '')) {
            $result = $model->save();
        }

        return $this->asJson($result);
    }



    /**
     * Отказ от цены.
     * Используется только владельцем объявления, когда вторая сторона предлагает
     * свою цену в третий раз.
     * @return string
     */
    public function actionCancelPrice() : Response
    {
        $result = [
            'result' => 'error',
        ];

        $model = new CancelPrice();
        if ($model->load(Yii::$app->request->post(), '')) {
            $result = $model->save();
        }

        return $this->asJson($result);
    }



}
