<?php
declare(strict_types=1);

namespace app\controllers\api\lot;

use Yii;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\Cors;
use yii\filters\VerbFilter;
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

        $tl = Lot::tableName();
        $to = Offer::tableName();

        $query = (new Query())
            ->select([
                "{$tl}.id",
                "{$tl}.deal",
                "{$tl}.price",
                "{$tl}.currency",
                "{$tl}.quantity",
                "{$tl}.period",
                "{$tl}.basis",
                "{$tl}.fob_port",
                "{$tl}.fob_terminal",
                "{$tl}.cif_country",
                "{$tl}.cif_port",
                "{$tl}.moisture",
                "{$tl}.foreign_matter",
                "{$tl}.grain_admixture",
                "{$tl}.gluten",
                "{$tl}.protein",
                "{$tl}.natural_weight",
                "{$tl}.falling_number",
                "{$tl}.vitreousness",
                "{$tl}.ragweed",
                "{$tl}.bug",
                "{$tl}.oil_content",
                "{$tl}.oil_admixture",
                "{$tl}.broken",
                "{$tl}.damaged",
                "{$tl}.dirty",
                "{$tl}.ash",
                "{$tl}.erucidic_acid",
                "{$tl}.peroxide_value",
                "{$tl}.acid_value",
                "{$tl}.link",
                "{$to}.status",         // статус офера (может: - не быть, - в ожидании, -"твердо")
            ])
            ->from($tl)
            ->where([
                "{$tl}.crop_id" => $crop_id,
                "{$tl}.status" => Lot::STATUS_ACTIVE, // статус объявлений для отображения на доске
            ])
            // подключаем таблицу только для того чтобы установить для объявления статус "твердо" если такой есть
            ->leftJoin("{$to}", "{$to}.lot_id = {$tl}.id AND {$to}.status = " . Offer::STATUS_AUCTION)
            ->orderBy([
                "{$tl}.created_at" => SORT_DESC,
                "{$tl}.id" => SORT_DESC,
            ]);

        if ($type_market) {
            $query->andWhere(["{$tl}.deal" => $type_market]);
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
                'title' => $lots[$i]['id'] . ' ' . Yii::t('app', 'crops.' . $crop->name),
                'deal' => strval($lots[$i]['deal']),
                'basis' => strval($lots[$i]['basis']),
                'price' => Yii::$app->formatter->asCurrency($lots[$i]['price'], $lots[$i]['currency']),
                'quantity' => strval($lots[$i]['quantity']),
                'period' => strval($lots[$i]['period']),
                'link' => strval($lots[$i]['link']),
                'status' => ((int) $lots[$i]['status'] === Offer::STATUS_AUCTION) ? false : true,
                'basis_location' => Lot::getBasisLocationArray($lots[$i]),
                'quality' => Lot::getStrQualityArray($lots[$i]),
            ];
        }

        return $this->asJson([
            'data' => $data,
            // 'count' => $data_provider->getCount(),
            // 'total_count' => $data_provider->getTotalCount(),
            // 'pagination' => $data_provider->getPagination()->getLinks(),
            // Текущая страница с рузельтатом. +1 так как отсчет начинается с 0
            'pagination_page' => $data_provider->getPagination()->getPage() + 1,
            // кол-во страниц с результатами
            'pagination_page_count' => $data_provider->getPagination()->getPageCount(),
            // 'pagination_page_size' => $data_provider->getPagination()->getPageSize(),

            // 'pagination_offset' => $data_provider->getPagination()->getOffset(),
            // 'pagination_limit' => $data_provider->getPagination()->getLimit()
        ]);
    }

}
