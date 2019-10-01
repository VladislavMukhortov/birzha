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
 * API информация о культуре для магазина
 */
class MarketShowController extends Controller
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
     * Информация о культуре по ID для магазина
     * @param  integer 'crop_id' ID культуры
     * @return string
     */
    public function actionIndex() : Response
    {
        $crop_id = Yii::$app->request->get('crop_id', 0);

        $crop = Crops::find()
            ->select('id, name')
            ->where(['id' => $crop_id])
            ->active()
            ->one();

        if (!$crop) {
            $crop = [];
        }

        $crop['name'] = Yii::t('app', 'crops.' . $crop['name']);

        return $this->asJson($crop);
    }



}
