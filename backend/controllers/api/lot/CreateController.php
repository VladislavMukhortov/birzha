<?php
declare(strict_types=1);

namespace app\controllers\api\lot;

use Yii;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpHeaderAuth;
use yii\web\Response;

use app\models\Lot;

/**
 * API
 */
class CreateController extends Controller
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
     * Список объявлений для доски
     * @param  integer 'crop_id'    ID культуры
     * @param  string  'deal'       тип 'buy'-покупка или 'sell'-продажа
     * @param  integer 'price'      цена
     * @param  string  'currency'   валюта
     * @param  integer 'quantity'   объем
     * @param  integer 'vat'        НДС
     * @param  string  'text'       дополнительная информация
     * @param  string  'basis'      базис
     * @return string
     */
    public function actionIndex() : Response
    {
        $deal = trim(Yii::$app->request->post('deal', ''));
        $crop_id = (int) Yii::$app->request->post('crop_id', 0);
        $currency = trim(Yii::$app->request->post('currency', ''));
        $price = (float) Yii::$app->request->post('price', 0);
        $quantity = (int) Yii::$app->request->post('quantity', 0);

        $moisture = (int) Yii::$app->request->post('moisture', 0);
        $foreign_matter = (int) Yii::$app->request->post('foreign_matter', 0);
        $grain_admixture = (int) Yii::$app->request->post('grain_admixture', 0);
        $gluten = (int) Yii::$app->request->post('gluten', 0);
        $protein = (int) Yii::$app->request->post('protein', 0);
        $natural_weight = (int) Yii::$app->request->post('natural_weight', 0);
        $falling_number = (int) Yii::$app->request->post('falling_number', 0);
        $vitreousness = (int) Yii::$app->request->post('vitreousness', 0);
        $ragweed = (int) Yii::$app->request->post('ragweed', 0);
        $bug = (int) Yii::$app->request->post('bug', 0);
        $oil_content = (int) Yii::$app->request->post('oil_content', 0);
        $oil_admixture = (int) Yii::$app->request->post('oil_admixture', 0);
        $broken = (int) Yii::$app->request->post('broken', 0);
        $damaged = (int) Yii::$app->request->post('damaged', 0);
        $dirty = (int) Yii::$app->request->post('dirty', 0);
        $ash = (int) Yii::$app->request->post('ash', 0);
        $erucidic_acid = (int) Yii::$app->request->post('erucidic_acid', 0);
        $peroxide_value = (int) Yii::$app->request->post('peroxide_value', 0);
        $acid_value = (int) Yii::$app->request->post('acid_value', 0);
        $other_color = (int) Yii::$app->request->post('other_color', 0);
        $crop_year = trim(Yii::$app->request->post('crop_year', ''));

        $basis = trim(Yii::$app->request->post('basis', ''));
        $fob_port = trim(Yii::$app->request->post('fob_port', ''));
        $fob_terminal = trim(Yii::$app->request->post('fob_terminal', ''));
        $cif_country = trim(Yii::$app->request->post('cif_country', ''));
        $cif_port = trim(Yii::$app->request->post('cif_port', ''));
        $period = trim(Yii::$app->request->post('period', ''));
        $text = trim(Yii::$app->request->post('text', ''));

        $deal = strtolower($deal);
        $currency = strtoupper($currency);
        $basis = strtoupper($basis);

        $data = [
            'success' => false
        ];

        if (
            $crop_id && $price && $currency && $quantity &&
            array_key_exists($deal, Lot::DEAL) &&
            array_key_exists($basis, Lot::BASIS)
        ) {
            $lot = new Lot;
            $lot->setUserId();
            $lot->setCompanyId();
            $lot->setCropId($crop_id);
            $lot->setDeal($deal);
            $lot->setPrice($price);
            $lot->setCurrency($currency);
            $lot->setQuantity($quantity);
            $lot->setVat($vat);
            $lot->setBasis($basis);
            $lot->setBasisFobPort($basis_fob_port);
            $lot->setBasisFobTerminal($basis_fob_terminal);
            $lot->setBasisCifCountry($basis_cif_country);
            $lot->setBasisCifPort($basis_cif_port);
            $lot->setText($text);
            $lot->setLink();
            $lot->setStatus();

            $transaction = Yii::$app->db->beginTransaction();

            try {

                if ($lot->save()) {
                    $data['success'] = true;
                }

                $transaction->commit();

            } catch(\Throwable $e) {
                $data['error'] = 'Возникла ошибка, попробуйте посже';
                $transaction->rollBack();
                throw $e;
            }
        }

        return $this->asJson($data);
    }



}
