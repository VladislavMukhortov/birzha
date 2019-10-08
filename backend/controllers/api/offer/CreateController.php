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

use app\models\Offer;
use app\models\Lot;

/**
 * API Создание оффера
 */
class CreateController extends Controller
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
     * Создание оффера начинается с подачи заявки на статус "твердо"
     * @param  string 'link' url объявления на которое делаем запрос
     * @return string
     */
    public function actionIndex() : Response
    {
        $link = trim(Yii::$app->request->post('link', ''));

        $output = [
            'success' => false
        ];

        if (!$link) {
            return $this->asJson($output);
        }

        $lot = Lot::find()->byLink($link)->active()->limit(1)->one();

        if ($lot) {

            // создатель объявления не равен обращающемуся
            if ($lot->company_id !== Yii::$app->user->identity->company_id) {

                $offer = Offer::find()->myActiveByLot($lot->id)->limit(1)->one();
                if ($offer) {
                    // оффер уже подан
                    $output['text'] = 'Вы уже подали заявку, ожидайте!';
                } else {
                    $offer = new Offer();
                    Offer::getDb()->transaction(function($db) use ($offer, $lot) {
                        $offer->lot_id = $lot->id;
                        $offer->lot_owner_id = $lot->company_id;
                        $offer->counterparty_id = Yii::$app->user->identity->company_id;
                        $offer->setLink();
                        $offer->setStatusWaiting();
                        $offer->save();
                    });

                    if (!$offer->hasErrors()) {
                        $output['success'] = true;
                        $output['text'] = 'Отлично! Вы подали заявку, ожидайте!';
                    }
                }
            }

        } else {
            $output['text'] = 'ОЙ! Объявление не найдено!';
        }

        return $this->asJson($output);
    }



}
