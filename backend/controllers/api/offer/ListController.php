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
 * API Создание оффера
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

        $query = (new Query())
            ->select([
                "{$tl}.id",                 // удалить, нельзя показывать
                // "{$tl}.company_id",         // удалить, нельзя показывать
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
                "{$tl}.link AS lot_link",

                "{$to}.counterparty_id",
                "{$to}.link AS offer_link",

                // выбираем максимальные значения, что бы из множества записей было выбранно корректное значение
                "MAX({$to}.status) AS status",
                "MAX({$to}.ended_at) AS ended_at",
            ])
            ->from($tl)
            ->where([
                "{$to}.status" => [Offer::STATUS_WAITING, Offer::STATUS_AUCTION],
            ])
            ->andWhere([
                "or",
                "{$to}.lot_owner_id = :my_company_id",
                "{$to}.counterparty_id = :my_company_id"
            ])
            ->rightJoin("{$to}", "{$to}.lot_id = {$tl}.id")
            ->orderBy([
                "status" => SORT_DESC,
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

        // return $this->asJson($offers);

        $crops = ArrayHelper::map(Crops::find()->allArray(), 'id', 'name');

        for ($i = 0, $count = count($offers); $i < $count; $i++) {
            $data[$i] = [
                'title' => $offers[$i]['id'] . ' ' . Yii::t('app', 'crops.' . $crops[$offers[$i]['crop_id']]),
                'deal' => strval($offers[$i]['deal']),
                'basis' => strval($offers[$i]['basis']),
                'price' => Yii::$app->formatter->asCurrency($offers[$i]['price'], $offers[$i]['currency']),
                'quantity' => strval($offers[$i]['quantity']),
                'period' => strval($offers[$i]['period']),
                // 'created_at' => strval($offers[$i]['created_at']),
                'status_s' => strval($offers[$i]['status']),
                'basis_location' => Lot::getBasisLocationArray($offers[$i]),
                'quality' => Lot::getStrQuality($offers[$i]),
            ];

            if ((int) $offers[$i]['status'] === Offer::STATUS_AUCTION) {
                $data[$i]['status'] = 'deals';
                $data[$i]['link'] = strval($offers[$i]['offer_link']);
                $data[$i]['link_text'] = 'Перейти в сделку';
                $data[$i]['desc'] = sprintf('"Твердо" до %s', $offers[$i]['ended_at']);
            } else {
                $data[$i]['link'] = strval($offers[$i]['lot_link']);

                if ((int) $offers[$i]['counterparty_id'] === Yii::$app->user->identity->company_id) {
                    $data[$i]['status'] = 'market';
                    $data[$i]['link_text'] = 'Посмотреть объявление';
                    $data[$i]['desc'] = 'Вы подали заявку на "твердо" к этому объявдению, ожидайте';
                } else {
                    $data[$i]['status'] = 'my';
                    $data[$i]['link_text'] = 'Посмотреть запросы к объявлению';
                    $data[$i]['desc'] = 'У вас запросили "твердо"';
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
