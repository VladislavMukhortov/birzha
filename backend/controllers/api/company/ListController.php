<?php
declare(strict_types=1);

namespace app\controllers\api\company;

use Yii;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\web\Response;

use yii\db\Query;
use yii\data\ActiveDataProvider;

use app\models\Company;

/**
 * API
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
     * Информация о пользователе по ID
     * @param  integer 'id' ID пользователя
     * @return string
     */
    public function actionIndex() : Response
    {
        $query = (new Query())
            ->select([
                'id',
                'name',
                'location',
                'swift',
                'iban'
            ])
            ->from(Company::tableName())
            ->where([
                'status' => Company::STATUS_ACTIVE
            ])
            ->orderBy([
                'id' => SORT_DESC
            ]);

        $data_provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Company::LIMIT_ON_PAGE
            ]
        ]);

        $traders = $data_provider->getModels();

        if (!$traders) {
            $traders = [];
        }

        return $this->asJson([
            'data' => $traders,
            // +1 так как отсчет начинается с 0
            'pagination_page' => $data_provider->getPagination()->getPage() + 1,
            'pagination_page_count' => $data_provider->getPagination()->getPageCount()
        ]);
    }



}
