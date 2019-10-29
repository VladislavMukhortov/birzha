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

        $lot = Lot::find()->my()->byLink($link)->notDeleted()->limit(1)->one();

        $output = [
            'result' => 'error'
        ];

        if ($lot) {
            $output['result'] = 'success';

            $offers_data = [];

            if ((int) $lot->status ===  Lot::STATUS_ACTIVE) {

                $offers = Offer::find()->imOwner()->byLot($lot->id)->auction()->all();

                for ($i = 0, $count = count($offers); $i < $count; $i++) {
                    $offers_data[$i] = [
                        'id' => $i + 1,
                        'link' => $offers[$i]->link,
                        'created_at' => $offers[$i]->created_at,
                    ];

                    $st = (int) $offers[$i]->status;

                    if ($st === Offer::STATUS_WAITING) {
                        $offers_data[$i]['status'] = false;
                        $offers_data[$i]['desc'] = 'STATUS_WAITING';
                    }
                    if ($st === Offer::STATUS_AUCTION) {
                        $offers_data[$i]['status'] = true;
                        $offers_data[$i]['desc'] = 'STATUS_AUCTION ' . $offers[$i]->ended_at;
                    }
                }
            }

            $output['data'] = $offers_data;
        }

        return $this->asJson($output);
    }

}
