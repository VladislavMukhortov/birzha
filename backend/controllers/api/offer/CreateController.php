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
                    'Origin' => Yii::$app->params['cors.origin'],
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
     * Создание оффера начинается с подачи заявки на статус "твердо"
     * @param  string 'link' url объявления на которое делаем запрос
     * @return string
     */
    public function actionRequest() : Response
    {
        $link = trim(strval(Yii::$app->request->post('link', '')));
        $lot = Lot::find()->byLink($link)->active()->limit(1)->one();

        $output = [
            'result' => 'error',
            'messages' => '',
        ];

        if (!$lot) {
            $output['messages'] = 'ОЙ! Объявление не найдено!';
            return $this->asJson($output);
        }

        // создатель объявления не равен обращающемуся
        if ((int) $lot->company_id === (int) Yii::$app->user->identity->company_id) {
            $output['messages'] = 'ОЙ! Объявление не найдено!';
            return $this->asJson($output);
        }

        // проверяем наличие запроса на "твердо" или статуса "твердо"
        $offer = Offer::find()->my()->byLot($lot->id)->waitingAndAuction()->limit(1)->one();

        if ($offer) {
            // оффер уже подан
            $output['messages'] = 'Вы уже подали заявку, ожидайте!';
            return $this->asJson($output);
        }
        
        $offer = new Offer();
        Offer::getDb()->transaction(function($db) use ($offer, $lot) {
            $offer->lot_id = $lot->id;
            $offer->lot_owner_id = $lot->company_id;
            $offer->counterparty_id = Yii::$app->user->identity->company_id;
            $offer->user_owner_id = $lot->user_id;
            $offer->user_counterparty_id = Yii::$app->user->identity->id;
            $offer->setLink();
            $offer->setStatusWaiting();
            $offer->save();
        });

        if ($offer->hasErrors()) {
            $output['messages'] = 'Ой! Возникла ошибка, попробуйте позже';
            return $this->asJson($output);
        }

        $output['result'] = 'success';
        $output['messages'] = 'Отлично! Вы подали заявку, ожидайте!';

        return $this->asJson($output);
    }



    /**
     * Дать "твердо"
     * @param  string 'link' url оффера которому двем "твердо"
     * @return string
     */
    public function actionAuction() : Response
    {
        $time = (int) Yii::$app->request->post('time', 0);
        $link = trim(strval(Yii::$app->request->post('link', '')));
        $offer = Offer::find()->imOwner()->byLink($link)->waiting()->limit(1)->one();

        $output = [
            'result' => 'error',
            'messages' => '',
        ];

        if (!$offer) {
            $output['messages'] = 'ОЙ! Запрос не найден!';
            return $this->asJson($output);
        }

        $other_offer = Offer::find()->imOwner()->byLot($offer->lot_id)->hasAuction();
        // уже есть оффер к этому объявлению со статусом "твердо"
        if ($other_offer) {
            $output['messages'] = 'Может быть только один активный статус твердо';
            return $this->asJson($output);
        }

        Offer::getDb()->transaction(function($db) use ($offer, $time) {
            $offer->setEndedAt($time);
            $offer->setStatusAuction();
            $offer->save();
        });

        if ($offer->hasErrors()) {
            $output['messages'] = 'Ой! Возникла ошибка, попробуйте позже';
            return $this->asJson($output);
        }

        $output['result'] = 'success';
        $output['messages'] = 'Отлично! Вы дали твердо!';

        return $this->asJson($output);
    }


}
