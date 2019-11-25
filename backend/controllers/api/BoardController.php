<?php
declare(strict_types=1);

namespace app\controllers\api;

use Yii;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpHeaderAuth;
use yii\web\Response;

/**
 * API
 */
class BoardController extends Controller
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
     * GET
     */
    public function actionIndex() : Response
    {

        $basis = [
            'EXW' => 'EXW',
            'FCA' => 'FCA',
            'CPT' => 'CPT',
            'CIP' => 'CIP',
            'DAT' => 'DAT',
            'DAP' => 'DAP',
            'DDP' => 'DDP',
            'FAS' => 'FAS',
            'FOB' => 'FOB',
            'CFR' => 'CFR',
            'CIF' => 'CIF'
        ];

        if (in_array('FOB', $basis)) {
            echo 'FOB yes '. PHP_EOL;
        } else echo 'FOB no '. PHP_EOL;

        if (in_array('fob', $basis)) {
            echo 'fob yes '. PHP_EOL;
        } else echo 'fob no '. PHP_EOL;

        var_dump(array_search('FOB', $basis));
        echo PHP_EOL;
        var_dump(array_search('fob', $basis));
        echo PHP_EOL;
        var_dump(array_key_exists('FOB', $basis));
        echo PHP_EOL;
        var_dump(array_key_exists('fob', $basis));
        echo PHP_EOL;

        return $this->asJson(true);
    }

}
