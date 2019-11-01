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

use yii\db\Query;
use yii\data\ActiveDataProvider;

use app\models\Offer;
use app\models\Lot;
use app\models\Crops;

/**
 * API Списки офферов
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
            ],

            'authenticator' => [
                'class' => HttpHeaderAuth::className(),
                'except' => ['options']
            ],
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
     * Загрушка на контроллер
     * @return [type] [description]
     */
    public function actionIndex() : Response
    {
        return $this->asJson([]);
    }



    /**
     * Список сделок (офферов) которые имеют "твердо" или ожидают "твердо"
     * @return string
     */
    public function actionAuction() : Response
    {
        $tl = Lot::tableName();
        $to = Offer::tableName();

        // получаю информацию об объявлениях которые ожидают "твердо" или в статусе "твердо"
        $query = (new Query())
            ->select([
                "{$tl}.*",

                // ID компании которая подала объявление
                // что бы указать пользователю что у него запросили "твердо" или он запросил "твердо"
                "{$to}.lot_owner_id",

                // выбираем максимум из значений (2 - ожидает "твердо" или 3 в статусе "твердо") что бы
                // показать оффер с "твердо" выше остальных
                "MAX({$to}.status) AS offer_status",
            ])
            ->from($to)
            ->where([
                "{$to}.status" => [Offer::STATUS_WAITING, Offer::STATUS_AUCTION],
            ])
            ->andWhere([
                "or",
                "{$to}.lot_owner_id = :my_company_id",
                "{$to}.counterparty_id = :my_company_id",
            ])
            ->leftJoin("{$tl}", "{$tl}.id = {$to}.lot_id")
            ->orderBy([
                "offer_status" => SORT_DESC,
                "{$to}.created_at" => SORT_DESC,
                "{$to}.id" => SORT_DESC,
            ])
            // груперуем по ID объявления, что бы исключить дубликаты своего объявления
            ->groupBy("{$to}.lot_id")
            ->params([
                ':my_company_id' => Yii::$app->user->identity->company_id
            ]);

        $data_provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Offer::OFFER_ON_PAGE
            ]
        ]);

        $offers = $data_provider->getModels();
        $data = [];

        /**
         * Получаем офферы которые в статусе "твердо"
         */
        $lot_ids = ArrayHelper::getColumn($offers, 'id');
        $offers_auction = Offer::find($lot_ids)
            ->select(['lot_id', 'link', 'status', 'ended_at'])
            ->auction()
            ->all();
        $offers_auction = ArrayHelper::index($offers_auction, 'lot_id');

        // названия культур
        $crops = ArrayHelper::map(Crops::find()->allArray(), 'id', 'name');

        for ($i = 0, $count = count($offers); $i < $count; $i++) {
            $data[$i] = [
                'title' => Yii::t('app', 'crops.' . $crops[$offers[$i]['crop_id']]),
                'deal' => strval($offers[$i]['deal']),
                'price' => Yii::$app->formatter->asCurrency($offers[$i]['price'], $offers[$i]['currency']),
                'quantity' => (int) $offers[$i]['quantity'],
                'period' => strval($offers[$i]['period']),
                'link' => strval($offers[$i]['link']),
                'basis' => strval($offers[$i]['basis']),
                'basis_location' => Lot::getBasisLocationArray($offers[$i]),
                'quality' => Lot::getStrQuality($offers[$i]),
                'is_my' => false,       // объявление принадлежить тому кто запрашивает
                'is_auction' => false,  // статус оффера объявления "твердо"
            ];

            // оффер в статусе "твердо"
            if (isset($offers_auction[$offers[$i]['id']])) {
                $data[$i]['is_auction'] = true;
                $data[$i]['link'] = strval($offers_auction[$offers[$i]['id']]['link']);
                $data[$i]['desc'] = sprintf('"Твердо" до %s', $offers_auction[$offers[$i]['id']]['ended_at']);
            } else {
                // Запросили "твердо" к моему объявлению
                if ((int) $offers[$i]['lot_owner_id'] === Yii::$app->user->identity->company_id) {
                    $data[$i]['is_my'] = true;
                    $data[$i]['desc'] = 'У вас запросили "твердо"';
                } else {
                    $data[$i]['desc'] = 'Вы подали заявку на "твердо" к этому объявдению, ожидайте';
                }
            }
        }

        return $this->asJson([
            'data' => $data,
            'pagination_page_count' => $data_provider->getPagination()->getPageCount(), // кол-во страниц с результатами
        ]);
    }



    /**
     * Список сделок (офферов) которые в статусе переписки между сторонами
     * стороны при этом не видят контакты друг друга
     * @return string
     */
    public function actionCommunication() : Response
    {
        /*$tl = Lot::tableName();
        $to = Offer::tableName();

        $query = (new Query())
            ->select([
                "{$tl}.id",                 // удалить, нельзя показывать
                "{$tl}.company_id",         // удалить, нельзя показывать
                "{$tl}.crop_id",
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
                "{$to}.counterparty_id",
                "{$to}.link",
                "{$to}.status",
                "{$to}.created_at",
                "{$to}.ended_at",
            ])
            ->from($tl)
            ->where([
                "{$to}.status" => Offer::STATUS_COMMUNICATION,
            ])
            ->andWhere(["or", "{$to}.lot_owner_id = :company_id", "{$to}.counterparty_id = :company_id"])
            ->rightJoin("{$to}", "{$to}.lot_id = {$tl}.id")
            ->orderBy([
                "{$to}.created_at" => SORT_DESC,
                "{$to}.id" => SORT_DESC,
            ])
            ->params([':company_id' => Yii::$app->user->identity->company_id]);

        $data_provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Offer::OFFER_ON_PAGE
            ]
        ]);

        $offers = $data_provider->getModels();
        $data = [];

        $crops = Crops::find()->allArray();
        $crops = ArrayHelper::map($crops, 'id', 'name');

        for ($i = 0, $count = count($offers); $i < $count; $i++) {
            $data[$i] = [
                // 'id' => strval($offers[$i]['id']),                    // удалить, нельзя показывать
                // 'company_id' => strval($offers[$i]['company_id']),    // удалить, нельзя показывать
                'title' => Yii::t('app', 'crops.' . $crops[$offers[$i]['crop_id']]),
                'deal' => strval($offers[$i]['deal']),
                'basis' => strval($offers[$i]['basis']),
                'price' => Yii::$app->formatter->asCurrency($offers[$i]['price'], $offers[$i]['currency']),
                'quantity' => strval($offers[$i]['quantity']),
                'period' => strval($offers[$i]['period']),
                'link' => strval($offers[$i]['link']),
                'created_at' => strval($offers[$i]['created_at']),
                'basis_location' => Lot::getBasisLocationArray($offers[$i]),
                'quality' => Lot::getStrQuality($offers[$i]),
            ];
        }

        return $this->asJson([
            'data' => $data,
            'pagination_page_count' => $data_provider->getPagination()->getPageCount(), // кол-во страниц с результатами
        ]);*/
    }




    /**
     * Список сделок (офферов) завершенные
     * когда стороны договорились между собой
     * @return string
     */
    public function actionArchive() : Response
    {
        /*$tl = Lot::tableName();
        $to = Offer::tableName();

        $query = (new Query())
            ->select([
                "{$tl}.id",                 // удалить, нельзя показывать
                "{$tl}.company_id",         // удалить, нельзя показывать
                "{$tl}.crop_id",
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
                "{$to}.counterparty_id",
                "{$to}.link",
                "{$to}.status",
                "{$to}.created_at",
                "{$to}.ended_at",
            ])
            ->from($tl)
            ->where([
                "{$to}.status" => Offer::STATUS_COMPLETE,
            ])
            ->andWhere(["or", "{$to}.lot_owner_id = :company_id", "{$to}.counterparty_id = :company_id"])
            ->rightJoin("{$to}", "{$to}.lot_id = {$tl}.id")
            ->orderBy([
                "{$to}.created_at" => SORT_DESC,
                "{$to}.id" => SORT_DESC,
            ])
            ->params([':company_id' => Yii::$app->user->identity->company_id]);

        $data_provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Offer::OFFER_ON_PAGE
            ]
        ]);

        $offers = $data_provider->getModels();
        $data = [];

        $crops = Crops::find()->allArray();
        $crops = ArrayHelper::map($crops, 'id', 'name');

        for ($i = 0, $count = count($offers); $i < $count; $i++) {
            $data[$i] = [
                // 'id' => strval($offers[$i]['id']),                    // удалить, нельзя показывать
                // 'company_id' => strval($offers[$i]['company_id']),    // удалить, нельзя показывать
                'title' => Yii::t('app', 'crops.' . $crops[$offers[$i]['crop_id']]),
                'deal' => strval($offers[$i]['deal']),
                'basis' => strval($offers[$i]['basis']),
                'price' => Yii::$app->formatter->asCurrency($offers[$i]['price'], $offers[$i]['currency']),
                'quantity' => strval($offers[$i]['quantity']),
                'period' => strval($offers[$i]['period']),
                'link' => strval($offers[$i]['link']),
                'created_at' => strval($offers[$i]['created_at']),
                'basis_location' => Lot::getBasisLocationArray($offers[$i]),
                'quality' => Lot::getStrQuality($offers[$i]),
            ];
        }

        return $this->asJson([
            'data' => $data,
            'pagination_page_count' => $data_provider->getPagination()->getPageCount(), // кол-во страниц с результатами
        ]);*/
    }



}
