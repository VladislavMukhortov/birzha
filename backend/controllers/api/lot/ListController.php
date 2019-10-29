<?php
declare(strict_types=1);

namespace app\controllers\api\lot;

use Yii;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpHeaderAuth;
use yii\web\Response;

use yii\db\Query;
use yii\data\ActiveDataProvider;

use app\models\Lot;
use app\models\Crops;
use app\models\Offer;

/**
 * API список объявлений для доски
 */
class ListController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors() : array
    {
        $behaviors = [
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
        ];

        /**
         * если передается токен "x-api-key?, тогда проходим авторизацию по токену,
         * что бы получить данные пользователя который обращается к экшену
         * @var [type]
         */
        $user_api_key = Yii::$app->request->getHeaders()['x-api-key'] ?? null;
        if ($user_api_key) {
            $behaviors['authenticator'] = [
                'class' => HttpHeaderAuth::className(),
                'except' => ['options']
            ];
        }

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
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
     * @return [type] [description]
     */
    public function actionIndex() : Response
    {
        return $this->asJson();
    }



    /**
     * Список объявлений для доски
     *
     * Показываем список объявлений которые сейчас торгуются:
     * - у объявления есть специальный статус для отображения на доске
     * - объявление опубликовано и офферов к нему еще нет, офферы есть, оффер в статусе "твердо"
     *
     * @param  integer 'crop_id' ID культуры
     * @param  integer 'type_market' 'all'-все или 'buy'-покупка или 'sell'-продажа
     * @return string
     */
    public function actionMarket() : Response
    {
        $crop_id = (int) Yii::$app->request->get('crop_id', 0);
        $type_market = trim(Yii::$app->request->get('type_market', ''));
        $type_market = strtolower($type_market);

        if (!array_key_exists($type_market, Lot::DEAL)) {
            $type_market = false;
        }

        $query = (new Query())
            ->from(Lot::tableName())
            ->where([
                'crop_id' => $crop_id,
                'status' => Lot::STATUS_ACTIVE, // статус объявлений для отображения на доске
            ])
            ->orderBy([
                'created_at' => SORT_DESC,
                'id' => SORT_DESC,
            ]);

        if ($type_market) {
            $query->andWhere(['deal' => $type_market]);
        }

        $data_provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Lot::LOT_ON_PAGE
            ]
        ]);

        $lots = $data_provider->getModels();
        $data = [];

        $crop = Crops::findOne($crop_id);

        for ($i = 0, $count = count($lots); $i < $count; $i++) {
            $data[$i] = [
                'title' => Yii::t('app', 'crops.' . $crop->name),
                'deal' => strval($lots[$i]['deal']),
                'price' => Yii::$app->formatter->asCurrency($lots[$i]['price'], $lots[$i]['currency']),
                'quantity' => (int) $lots[$i]['quantity'],
                'period' => strval($lots[$i]['period']),
                'link' => strval($lots[$i]['link']),
                'basis' => strval($lots[$i]['basis']),
                'basis_location' => Lot::getBasisLocationArray($lots[$i]),
                'quality' => Lot::getStrQuality($lots[$i]),
            ];
        }

        return $this->asJson([
            'data' => $data,
            'pagination_page_count' => $data_provider->getPagination()->getPageCount(), // кол-во страниц с результатами
        ]);
    }



    /**
     * Список личных объявлений
     * Показываем актуальные объявления которые:
     * - отображаются на доске
     * - в статусе твердо
     * - на этапе общения
     *
     * @return
     */
    public function actionMyOrders() : Response
    {
        $query = (new Query())
            ->from(Lot::tableName())
            ->where([
                'company_id' => Yii::$app->user->identity->company_id,
                'status' => [
                    Lot::STATUS_WAITING,
                    Lot::STATUS_ACTIVE,
                    Lot::STATUS_COMMUNICATION,
                ],
            ])
            ->orderBy([
                'created_at' => SORT_DESC,
                'id' => SORT_DESC,
            ]);

        $data_provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Lot::LOT_ON_PAGE
            ]
        ]);

        $lots = $data_provider->getModels();
        $data = [];

        $lot_ids = ArrayHelper::getColumn($lots, 'id');

        /**
         * Получаем офферы которые в статусе "твердо"
         */
        $offers_auction = Offer::find()
            ->select(['lot_id', 'link'])
            ->where([
                'lot_id' => $lot_ids,
                'status' => Offer::STATUS_AUCTION,
            ])
            ->imOwner()
            ->all();
        $offers_auction = ArrayHelper::index($offers_auction, 'lot_id');

        /**
         * Получаем кол-во офферов которые ожидают подтверждения "твердо"
         */
        $offers_waiting = Offer::find()
            ->select(['lot_id', 'COUNT(id) AS o_count'])
            ->where([
                'lot_id' => $lot_ids,
                'status' => Offer::STATUS_WAITING,
            ])
            ->imOwner()
            ->groupBy('lot_id')
            ->createCommand()
            ->queryAll();
        $offers_waiting = ArrayHelper::index($offers_waiting, 'lot_id');

        // список культур
        $crops = ArrayHelper::map(Crops::find()->allArray(), 'id', 'name');

        for ($i = 0, $count = count($lots); $i < $count; $i++) {
            $data[$i] = [
                'title' => Yii::t('app', 'crops.' . $crops[$lots[$i]['crop_id']]),
                'deal' => strval($lots[$i]['deal']),
                'price' => Yii::$app->formatter->asCurrency($lots[$i]['price'], $lots[$i]['currency']),
                'quantity' => (int) $lots[$i]['quantity'],
                'period' => strval($lots[$i]['period']),
                'link' => strval($lots[$i]['link']),
                'created_at' => strval($lots[$i]['created_at']),
                'basis' => strval($lots[$i]['basis']),
                'basis_location' => Lot::getBasisLocationArray($lots[$i]),
                'quality' => Lot::getStrQuality($lots[$i]),
                'is_edit' => true,      // возможность редактирования
                'is_remove' => true,    // возможность удаления
                'is_auction' => false,  // имеется ли оффер
                'waiting_offer_count' => 0,  // кол-во запросов "твердо"
            ];

            if (isset($offers_auction[$lots[$i]['id']])) {
                $data[$i]['is_edit'] = false;
                $data[$i]['is_remove'] = false;
                $data[$i]['is_auction'] = true;
                $data[$i]['offer_link'] = $offers_auction[$lots[$i]['id']]['link'];
            }

            if (isset($offers_waiting[$lots[$i]['id']])) {
                $data[$i]['waiting_offer_count'] = (int) $offers_waiting[$lots[$i]['id']]['o_count'];
            }
        }

        return $this->asJson([
            'data' => $data,
            'pagination_page_count' => $data_provider->getPagination()->getPageCount(), // кол-во страниц с результатами
        ]);
    }

}
