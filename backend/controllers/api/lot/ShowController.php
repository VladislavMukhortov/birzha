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

use yii\db\Query;

use app\models\Lot;
use app\models\Crops;
use app\models\Offer;

/**
 * API Показываем объявление для доски
 */
class ShowController extends Controller
{

    public $enableCsrfValidation = false;

    /**
     * {@inheritdoc}
     */
    public function behaviors() : array
    {
        $behaviors = [
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
        ];

        /**
         * если передается токен "x-api-key?, тогда проходим авторизацию по токену,
         * что бы получить данные пользователя который обращается к экшену
         * @var [type]
         */
        $user_api_key = Yii::$app->request->getHeaders()['x-api-key'] ?? null;
        if ($user_api_key) {
            $behaviors['authenticator'] = [
                'class' => HttpHeaderAuth::className(),
                'except' => ['options']
            ];
        }

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
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
     * @return [type] [description]
     */
    public function actionIndex() : Response
    {
        return $this->asJson();
    }



    /**
     * Отдаем данные объявления для пользователя который будет взаимодействовать с ним
     *
     * Не показывать данные о личности которая сделала объявление
     *
     * Объявление можеть быть:
     * - 404 (если объявление уже не показывается на доске из-за того что было удалено или уже сторговалось)
     * - доступно для взаимодействия (запроса статуса "твердо")
     * - ожидание статуса "твердо"
     * - "твердо" с тобой и владельцем объявления
     * - объявление смотрит создатель
     * - объявление смотрит неавторизированный пользователь
     *
     * @param  string 'link' ссылка культуры
     * @return string
     */
    public function actionMarket() : Response
    {
        $link = trim(strval(Yii::$app->request->get('link', '')));
        $lot = Lot::find()->byLink($link)->active()->limit(1)->one();

        $output = [
            'result' => 'error',
        ];

        if ($lot) {

            $output['result'] = 'success';
            $output['lot'] = Lot::getFullInfo($lot);

            /**
             * Возможность взаимодействовать с объявлением
             * "free"    - свободно, возможность запросить "твердо"
             * "wait"    - ожидать "твердо" (уже подал заявку, жди)
             * "auction" - "твердо" с тобой и владельцем объявления
             * ""        - объявление смотрит создавший его
             * ""        - объявление смотрит неавторизированный пользователь
             *
             * Когда лот будет на страдии переписки сторон, то объявление не будет найдено
             * и будет 404 ошибка
             * @var string
             */
            $offer = '';
            /**
             * ссылка на сделку
             * @var string|false
             */
            $offer_link = false;
            /**
             * время окончания сделки в статусе "твердо"
             * @var boolean
             */
            $offer_ended_at = false;
            if (!Yii::$app->user->isGuest && $lot->company_id !== Yii::$app->user->identity->company_id) {
                $offer = 'free';
                $my_offer = Offer::find()->my()->byLot($lot->id)->waitingAndAuction()->limit(1)->one();
                if ($my_offer) {
                    $offer = ($my_offer->status === Offer::STATUS_AUCTION) ? 'auction' : 'wait';
                    $offer_link = $my_offer->link;
                    $offer_ended_at = $my_offer->ended_at;
                }
            }

            $output['lot']['offer'] = $offer;

            if ($offer_link) {
                $output['lot']['offer_link'] = strval($offer_link);
            }

            if ($offer_ended_at) {
                $output['lot']['offer_ended_at'] = Yii::$app->formatter->asDatetime($offer_ended_at);
            }

        }

        return $this->asJson($output);
    }


    /**
     * Данные об объявлении для просмотра/редактирования
     * @param  string 'link' ссылка объявления
     * @return string
     */
    public function actionMyOrder() : Response
    {
        $link = trim(strval(Yii::$app->request->get('link', '')));

        $lot = Lot::find()->my()->byLink($link)->notDeleted()->limit(1)->one();

        $output = [
            'result' => 'error'
        ];

        if ($lot) {
            $output['result'] = 'success';

            // наличие оффера не дает разрешения редактировать объявление
            $offer = Offer::find()->byLot($lot->id)->hasEditLot();

            $output['data'] = [
                'cant_edit' => (boolean) $offer,
                'crop_id' => strval($lot->crop_id),
                'deal' => strval($lot->deal),
                'price' => strval($lot->price),
                'currency' => strval($lot->currency),
                'quantity' => strval($lot->quantity),
                'period' => strval($lot->period),
                'basis' => strval($lot->basis),
                'fob_port' => strval($lot->fob_port),
                'fob_terminal' => strval($lot->fob_terminal),
                'cif_country' => strval($lot->cif_country),
                'cif_port' => strval($lot->cif_port),
                'moisture' => strval($lot->moisture),
                'foreign_matter' => strval($lot->foreign_matter),
                'grain_admixture' => strval($lot->grain_admixture),
                'gluten' => strval($lot->gluten),
                'protein' => strval($lot->protein),
                'natural_weight' => strval($lot->natural_weight),
                'falling_number' => strval($lot->falling_number),
                'vitreousness' => strval($lot->vitreousness),
                'ragweed' => strval($lot->ragweed),
                'bug' => strval($lot->bug),
                'oil_content' => strval($lot->oil_content),
                'oil_admixture' => strval($lot->oil_admixture),
                'broken' => strval($lot->broken),
                'damaged' => strval($lot->damaged),
                'dirty' => strval($lot->dirty),
                'ash' => strval($lot->ash),
                'erucidic_acid' => strval($lot->erucidic_acid),
                'peroxide_value' => strval($lot->peroxide_value),
                'acid_value' => strval($lot->acid_value),
                'other_color' => strval($lot->other_color),
                'w' => strval($lot->w),
                'crop_year' => strval($lot->crop_year),
                'text' => strval($lot->text),
            ];
        }

        return $this->asJson($output);
    }



    /**
     * Данные объявления и оффера для взаимодействия в статусе "твердо"
     * @param  string 'link' ссылка оффера
     * @return
     */
    public function actionAuction() : Response
    {
        $link = trim(strval(Yii::$app->request->get('link', '')));

        // офер
        $offer = Offer::find()->byLink($link)->auction()->limit(1)->one();

        $output = [
            'result' => 'error',
        ];

        if ($offer) {

            $lot = Lot::find()->byId($offer->lot_id)->active()->limit(1)->one();

            if ($lot) {
                $output['result'] = 'success';
                $output['lot'] = Lot::getFullInfo($lot);

                $require_price_1 = (float) $offer['require_price_1'];
                $require_price_2 = (float) $offer['require_price_2'];
                $require_price_3 = (float) $offer['require_price_3'];
                $lot_price_1 = (float) $offer['lot_price_1'];
                $lot_price_2 = (float) $offer['lot_price_2'];

                $output['offer'] = [
                    /**
                     * Определяем кто смотрит страницу, владелец объявления или контрагент(вторая сторона)
                     */
                    'lot_owner' => ((int) $offer['lot_owner_id'] === (int) Yii::$app->user->identity->company_id) ? true : false,
                    'link' => strval($offer['link']),
                    'deal' => strval($lot['deal']),
                    'currency' => strval($lot['currency']),
                    'lot_owner_id' => $offer['lot_owner_id'], // DELETE
                    'counterparty_id' => $offer['counterparty_id'], // DELETE
                    'ended_at' => Yii::$app->formatter->asDatetime($offer['ended_at']),
                    'price' => [
                        'require_1' => ($require_price_1) ? Yii::$app->formatter->asCurrency($require_price_1, $lot['currency']) : '',
                        'require_2' => ($require_price_2) ? Yii::$app->formatter->asCurrency($require_price_2, $lot['currency']) : '',
                        'require_3' => ($require_price_3) ? Yii::$app->formatter->asCurrency($require_price_3, $lot['currency']) : '',
                        'lot_1' => ($lot_price_1) ? Yii::$app->formatter->asCurrency($lot_price_1, $lot['currency']) : '',
                        'lot_2' => ($lot_price_2) ? Yii::$app->formatter->asCurrency($lot_price_2, $lot['currency']) : '',
                    ],
                    'price_offer' => $offer->priceOfferInAuction(),
                ];
            }
        }

        return $this->asJson($output);
    }

}
