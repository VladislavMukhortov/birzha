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

use app\models\Lot;
// use app\models\Company;
use app\models\Crops;

/**
 * API Объявление для доски
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
     * Данные об объявлении
     * @param  integer $id ID культуры
     * @return string
     */
    public function actionIndex() : Response
    {
        $lot_link = trim(Yii::$app->request->get('link', ''));

        $lot = static::find()
            ->where([
                'link' => $lot_link,
                'status' => Lot::STATUS_ACTIVE
            ])
            ->one();

        $lot_output = [
            'success' => false
        ];

        if ($lot) {

            $crop_name = Crops::find()
                ->select('name')
                ->where(['id' => $lot->crop_id])
                ->scalar();

            if ($crop_name) {
                $crop_name = Yii::t('app', 'crops.' . $crop_name);
            }

            $lot_output['success'] = true;

            $lot_output['title'] = strval($crop_name),
            $lot_output['deal'] => strval($lot->deal),
            $lot_output['basis'] => strval($lot->basis),
            $lot_output['price'] => strval($lot->price . ' ' . $lot->currency),
            $lot_output['quantity'] => strval($lot->quantity),
            $lot_output['period'] => strval($lot->period),
            $lot_output['created_at'] => strval($lot->created_at),

            switch ($lot->basis) {
                case Lot::BASIS['FOB']:
                    $lot_output['parity'] = $lot->fob_port . ', ' . $lot->fob_terminal;
                    break;
                case Lot::BASIS['CIF']:
                    $lot_output['parity'] = $lot->cif_country . ', ' . $lot->cif_port;
                    break;
                default:
                    $lot_output['parity'] = '';
                    break;
            }

            // $lot['text'] = $lot['text'] ?? '';
            $lot_output['text'] = strval($lot['text']);

            $lot_output['quality'] = [
                'moisture'          => strval($lot['moisture']),
                'foreign_matter'    => strval($lot['foreign_matter']),
                'grain_admixture'   => strval($lot['grain_admixture']),
                'gluten'            => strval($lot['gluten']),
                'protein'           => strval($lot['protein']),
                'natural_weight'    => strval($lot['natural_weight']),
                'falling_number'    => strval($lot['falling_number']),
                'vitreousness'      => strval($lot['vitreousness']),
                'ragweed'           => strval($lot['ragweed']),
                'bug'               => strval($lot['bug']),
                'oil_content'       => strval($lot['oil_content']),
                'oil_admixture'     => strval($lot['oil_admixture']),
                'broken'            => strval($lot['broken']),
                'damaged'           => strval($lot['damaged']),
                'dirty'             => strval($lot['dirty']),
                'ash'               => strval($lot['ash']),
                'erucidic_acid'     => strval($lot['erucidic_acid']),
                'peroxide_value'    => strval($lot['peroxide_value']),
                'acid_value'        => strval($lot['acid_value']),
                'other_color'       => strval($lot['other_color']),
                'crop_year'         => strval($lot['crop_year']),
            ];

        } else {
            $lot_output['error'] = 'Lot not found';
        }

        return $this->asJson($lot_output);
    }

}
