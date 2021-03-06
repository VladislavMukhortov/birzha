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
        $type_market = strtolower(trim(Yii::$app->request->get('type_market', '')));

        $query = Lot::find()->byCropId($crop_id)->active()->orderByCreatedAt();

        if (array_key_exists($type_market, Lot::DEAL)) {
            $query->byDeal($type_market);
        }

        $data_provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Lot::LOT_ON_PAGE,
            ],
        ]);

        $lots = $data_provider->getModels();

        $crop = Crops::findOne($crop_id);

        $data = [];
        for ($i = 0, $count = count($lots); $i < $count; $i++) {
            $data[$i] = Lot::getShortInfoArray($lots[$i]);
            $data[$i]['title'] = Yii::t('app', 'crops.' . $crop->name);
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
     *
     * @return
     */
    public function actionMyActiveOrders() : Response
    {
        $query = Lot::find()->my()->active()->orderByCreatedAt();

        $data_provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Lot::LOT_ON_PAGE,
            ],
        ]);

        $lots = $data_provider->getModels();

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

        $data = [];
        for ($i = 0, $count = count($lots); $i < $count; $i++) {
            $data[$i] = Lot::getShortInfoArray($lots[$i]);
            $data[$i]['title'] = Yii::t('app', 'crops.' . $crops[$lots[$i]['crop_id']]);
            $data[$i]['timeZone'] = Yii::$app->formatter->timeZone;
            $data[$i]['created_at'] = Yii::$app->formatter->asDatetime($lots[$i]['created_at']);
            $data[$i]['is_edit'] = true;            // возможность редактирования
            $data[$i]['is_remove'] = true;          // возможность удаления
            $data[$i]['is_auction'] = false;        // имеется ли оффер
            $data[$i]['waiting_offer_count'] = 0;   // кол-во запросов "твердо"

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



    /**
     * Список личных объявлений
     * Показываем объявления которые находятся в архиве
     *
     * @return
     */
    public function actionMyArchiveOrders() : Response
    {
        $query = Lot::find()->my()->archive()->orderByCreatedAt();

        $data_provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Lot::LOT_ON_PAGE,
            ],
        ]);

        $lots = $data_provider->getModels();

        // список культур
        $crops = ArrayHelper::map(Crops::find()->allArray(), 'id', 'name');

        $data = [];
        for ($i = 0, $count = count($lots); $i < $count; $i++) {
            $data[$i] = Lot::getShortInfoArray($lots[$i]);
            $data[$i]['title'] = Yii::t('app', 'crops.' . $crops[$lots[$i]['crop_id']]);
            $data[$i]['created_at'] = Yii::$app->formatter->asDatetime($lots[$i]['created_at']);
        }

        return $this->asJson([
            'data' => $data,
            'pagination_page_count' => $data_provider->getPagination()->getPageCount(), // кол-во страниц с результатами
        ]);
    }



    /**
     * Список личных объявлений
     * Показываем объявления которые используются в оффере, который
     * находится в статусе переписки двух сторон
     *
     * @return
     */
    /*public function actionMyCommunicationOrders() : Response
    {
        $query = Lot::find()->my()->communication()->orderByCreatedAt();

        $data_provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Lot::LOT_ON_PAGE,
            ],
        ]);

        $lots = $data_provider->getModels();

        // список культур
        $crops = ArrayHelper::map(Crops::find()->allArray(), 'id', 'name');

        $data = [];
        for ($i = 0, $count = count($lots); $i < $count; $i++) {
            $data[$i] = Lot::getShortInfoArray($lots[$i]);
            $data[$i]['title'] = Yii::t('app', 'crops.' . $crops[$lots[$i]['crop_id']]);
            $data[$i]['created_at'] = Yii::$app->formatter->asDatetime($lots[$i]['created_at']);
        }

        return $this->asJson([
            'data' => $data,
            'pagination_page_count' => $data_provider->getPagination()->getPageCount(), // кол-во страниц с результатами
        ]);
    }*/



    /**
     * Список личных объявлений
     * Показываем объявления которые были закрыты, так как сделка состоялась
     * @return
     */
    /*public function actionMyCompleteOrders() : Response
    {
        $query = Lot::find()->my()->complete()->orderByCreatedAt();

        $data_provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Lot::LOT_ON_PAGE,
            ],
        ]);

        $lots = $data_provider->getModels();

        // список культур
        $crops = ArrayHelper::map(Crops::find()->allArray(), 'id', 'name');

        $data = [];
        for ($i = 0, $count = count($lots); $i < $count; $i++) {
            $data[$i] = Lot::getShortInfoArray($lots[$i]);
            $data[$i]['title'] = Yii::t('app', 'crops.' . $crops[$lots[$i]['crop_id']]);
            $data[$i]['created_at'] = Yii::$app->formatter->asDatetime($lots[$i]['created_at']);
        }

        return $this->asJson([
            'data' => $data,
            'pagination_page_count' => $data_provider->getPagination()->getPageCount(), // кол-во страниц с результатами
        ]);
    }*/



}
