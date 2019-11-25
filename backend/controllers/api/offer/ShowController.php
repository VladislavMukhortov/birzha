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
 * API Информация об офферах
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



    /**
     * @return string
     */
    public function actionIndex() : Response
    {
        return $this->asJson([]);
    }



    /**
     * Информация об офферах к обхявлению
     * @param  string 'link' ссылка объявления
     * @return string
     */
    public function actionMyOrder() : Response
    {
        $link = trim(strval(Yii::$app->request->get('link', '')));
        $lot = Lot::find()->my()->byLink($link)->active()->limit(1)->one();

        $output = [
            'result' => 'error',
        ];

        if ($lot) {
            $output['result'] = 'success';
            $output['data'] = [];

            // офферы к объявлению
            $offers = Offer::find()->byLot($lot->id)->waitingAndAuction()->all();

            for ($i = 0, $count = count($offers); $i < $count; $i++) {
                $output['data'][$i] = [
                    'link' => strval($offers[$i]['link']),
                    'created_at' => Yii::$app->formatter->asDatetime($offers[$i]['created_at']),
                    'ended_at' => Yii::$app->formatter->asDatetime($offers[$i]['ended_at']),
                    'status' => ((int) $offers[$i]['status'] === Offer::STATUS_AUCTION) ? true : false,
                    'time' => Offer::DEFAULT_AUCTION_TIME,
                ];
            }
        }

        return $this->asJson($output);
    }

}
