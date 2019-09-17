<?php
declare(strict_types=1);

namespace app\controllers\api\crop;

use Yii;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\web\Response;

use app\models\Crops;

/**
 * API
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
     * Список культур для доски объявлений
     * @return string
     */
    public function actionIndex() : Response
    {
        $crops = Crops::find()
            ->select('id, name')
            ->where([
                'status' => Crops::STATUS_ACTIVE
            ])
            ->orderBy(['sort' => SORT_ASC])
            ->asArray()
            ->all();

        if (!$crops) {
            $crops = [];
        }

        for ($i = 0, $arr = count($crops); $i < $arr; $i++) {
            $crops[$i]['name'] = Yii::t('app', 'crops.' . $crops[$i]['name']);
        }

        return $this->asJson($crops);
    }



}
