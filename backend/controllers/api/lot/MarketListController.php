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
class MarketListController extends Controller
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
     * Список объявлений для доски
     *
     * Показываем список объявлений которые сейчас торгуются:
     * - у объявления есть специальный статус для отображения на доске
     * - объявление опубликовано и офферов к нему еще нет, офферы есть, оффер в статусе "твердо"
     *
     * Объявление которое в статусе "твердо" просто отображается, но взаимодействовать с ним нельзя
     *
     * @param  integer 'crop_id' ID культуры
     * @param  integer 'type_market' 'all'-все или 'buy'-покупка или 'sell'-продажа
     * @return string
     */
    public function actionIndex() : Response
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
                "{$tl}.id",                 // удалить, нельзя показывать
                "{$tl}.company_id",         // удалить, нельзя показывать
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
                "{$to}.status",             // статус офера (может: - не быть, - в ожидании, -"твердо")
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
                "{$tl}.id" => SORT_DESC
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
        $lots_output = [];

        $crop = Crops::findOne($crop_id);

        for ($i = 0, $count = count($lots); $i < $count; $i++) {
            $lots_output[$i] = [
                'id' => strval($lots[$i]['id']),                    // удалить, нельзя показывать
                'company_id' => strval($lots[$i]['company_id']),    // удалить, нельзя показывать
                'title' => Yii::t('app', 'crops.' . $crop->name),
                'deal' => strval($lots[$i]['deal']),
                'basis' => strval($lots[$i]['basis']),
                'price' => Yii::$app->formatter->asCurrency($lots[$i]['price'], $lots[$i]['currency']),
                'quantity' => strval($lots[$i]['quantity']),
                'period' => strval($lots[$i]['period']),
                'link' => strval($lots[$i]['link']),
                'status' => ((int) $lots[$i]['status'] === Offer::STATUS_AUCTION) ? false : true,
                'parity' => Lot::getBasisLocationArray($lots[$i]),
            ];

            $param = '';

            if ($lots[$i]['moisture'])        { $param .= $lots[$i]['moisture'] . '%'; }              // влажность - 0-100%
            if ($lots[$i]['foreign_matter'])  { $param .= '/' . $lots[$i]['foreign_matter'] . '%'; }  // сорная примесь - 0-100%
            if ($lots[$i]['grain_admixture']) { $param .= '/' . $lots[$i]['grain_admixture'] . '%'; } // зерновая примесь - 0-100%
            if ($lots[$i]['gluten'])          { $param .= '/' . $lots[$i]['gluten'] . '%'; }          // клейковина - 12-40%
            if ($lots[$i]['protein'])         { $param .= '/' . $lots[$i]['protein'] . '%'; }         // протеин - 0-80%
            if ($lots[$i]['natural_weight'])  { $param .= '/' . $lots[$i]['natural_weight']; }        // натура - 50-1000 грам/литр
            if ($lots[$i]['falling_number'])  { $param .= '/' . $lots[$i]['falling_number']; }        // число падения - 50-500 штук
            if ($lots[$i]['vitreousness'])    { $param .= '/' . $lots[$i]['vitreousness'] . '%'; }    // стекловидность - 20-95%
            if ($lots[$i]['ragweed'])         { $param .= '/' . $lots[$i]['ragweed']; }               // амброзия - 0-500 штук/кг
            if ($lots[$i]['bug'])             { $param .= '/' . $lots[$i]['bug'] . '%'; }             // клоп - 0-20%
            if ($lots[$i]['oil_content'])     { $param .= '/' . $lots[$i]['oil_content'] . '%'; }     // масличность - 0-80%
            if ($lots[$i]['oil_admixture'])   { $param .= '/' . $lots[$i]['oil_admixture'] . '%'; }   // масличная примесь - 0-100%
            if ($lots[$i]['broken'])          { $param .= '/' . $lots[$i]['broken'] . '%'; }          // битые - 0-100%
            if ($lots[$i]['damaged'])         { $param .= '/' . $lots[$i]['damaged'] . '%'; }         // повреждённые - 0-100%
            if ($lots[$i]['dirty'])           { $param .= '/' . $lots[$i]['dirty'] . '%'; }           // маранные - 0-100%
            if ($lots[$i]['ash'])             { $param .= '/' . $lots[$i]['ash'] . '%'; }             // зольность - 0-100%
            if ($lots[$i]['erucidic_acid'])   { $param .= '/' . $lots[$i]['erucidic_acid'] . '%'; }   // эруковая кислота - 0-20%
            if ($lots[$i]['peroxide_value'])  { $param .= '/' . $lots[$i]['peroxide_value'] . '%'; }  // перекисное число - 0-20%
            if ($lots[$i]['acid_value'])      { $param .= '/' . $lots[$i]['acid_value'] . '%'; }      // кислотное число - 0-20%

            $lots_output[$i]['param'] = $param;
        }

        return $this->asJson([
            'data' => $lots_output,
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
