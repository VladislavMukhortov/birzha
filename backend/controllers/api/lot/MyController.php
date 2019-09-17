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
use app\models\Company;
use app\models\Crops;

/**
 * API
 */
class MyController extends Controller
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
     * @param  integer 'crop_id' ID культуры
     * @param  integer 'type_market' 'buy'-покупка или 'sell'-продажа
     * @return string
     */
    public function actionIndex() : Response
    {
        $crop_id = (int) Yii::$app->request->get('crop_id', 0);
        $type_market = trim(Yii::$app->request->get('type_market', ''));
        $type_market = strtolower($type_market);

        if (!array_key_exists($type_market, Lot::DEAL)) {
            $type_market = Lot::DEAL['sell'];
        }

        $tlot = Lot::tableName();
        $tcompany = Company::tableName();
        $tcrop = Crops::tableName();

        $query = (new Query())
            ->select([
                "{$tlot}.id",
                "{$tlot}.deal",
                "{$tlot}.price",
                "{$tlot}.currency",
                "{$tlot}.quantity",
                "{$tlot}.period",
                "{$tlot}.basis",
                "{$tlot}.fob_port",
                "{$tlot}.fob_terminal",
                "{$tlot}.cif_country",
                "{$tlot}.cif_port",
                "{$tlot}.created_at",
                "{$tcompany}.name AS trader_name",
                "{$tcrop}.name AS crop"
            ])
            ->from($tlot)
            ->where([
                "{$tlot}.crop_id" => $crop_id,
                "{$tlot}.deal" => $type_market,
                "{$tlot}.status" => Lot::STATUS_ACTIVE
            ])
            ->leftJoin("{$tcompany}", "{$tcompany}.id = {$tlot}.company_id")
            ->leftJoin("{$tcrop}", "{$tcrop}.id = {$tlot}.crop_id")
            ->orderBy([
                "{$tlot}.id" => SORT_DESC
            ]);

        $data_provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Lot::LIMIT_ON_PAGE
            ]
        ]);

        $lots = $data_provider->getModels();

        if (!$lots) {
            $lots = [];
        }

        for ($i = 0, $count = count($lots); $i < $count; $i++) {
            switch ($lots[$i]['basis']) {
                case Lot::BASIS['FOB']:
                    $lots[$i]['parity'] = $lots[$i]['fob_port'] . ', ' . $lots[$i]['fob_terminal'];
                    break;
                case Lot::BASIS['CIF']:
                    $lots[$i]['parity'] = $lots[$i]['cif_country'] . ', ' . $lots[$i]['cif_port'];
                    break;
                default:
                    $lots[$i]['parity'] = '';
                    break;
            }

            unset($lots[$i]['fob_port']);
            unset($lots[$i]['fob_terminal']);
            unset($lots[$i]['cif_country']);
            unset($lots[$i]['cif_port']);
        }

        return $this->asJson([
            'data' => $lots,
            'pagination_page' => $data_provider->getPagination()->getPage() + 1,
            'pagination_page_count' => $data_provider->getPagination()->getPageCount(),
        ]);
    }



}
