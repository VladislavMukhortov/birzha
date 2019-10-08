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
            'success' => false
        ];

        if ($lot) {

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
            if (!Yii::$app->user->isGuest && $lot->id !== Yii::$app->user->identity->company_id) {
                $offer = 'free';
                $my_offer = Offer::find()->myActiveByLot($lot->id)->limit(1)->one();
                if ($my_offer) {
                    $offer = ($my_offer->status === Offer::STATUS_AUCTION) ? 'auction' : 'wait';
                    $offer_link = strval($my_offer->link);
                    $offer_ended_at = strval($my_offer->ended_at);
                }
            }

            $crop = Crops::findOne($lot->crop_id);

            $output['success'] = true;
            $output['lot'] = [
                'title' => Yii::t('app', 'crops.' . $crop->name),
                'crop_id' => (int) $lot->crop_id,
                'deal' => strval($lot->deal),
                'basis' => strval($lot->basis),
                'price' => Yii::$app->formatter->asCurrency($lot->price, $lot->currency),
                'quantity' => strval($lot->quantity),
                'period' => strval($lot->period),
                'text' => strval($lot->text),
                'link' => strval($lot->link),
                'parity' => Lot::getBasisLocation($lot),
                'offer' => $offer,
                'quality' => [
                    [
                        'name' => Yii::t('app', 'crops.quality.moisture'),
                        'val' => strval($lot->moisture), // влажность - 0-100%
                    ],[
                        'name' => Yii::t('app', 'crops.quality.foreign_matter'),
                        'val' => strval($lot->foreign_matter), // сорная примесь - 0-100%
                    ]
                ],
            ];

            if ($offer_link) {
                $output['lot']['offer_link'] = $offer_link;
            }
            if ($offer_ended_at) {
                $output['lot']['offer_ended_at'] = $offer_ended_at;
            }

            if ($lot->crop_year) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.crop_year'),
                    'val' => strval($lot->crop_year), // год урожая
                ];
            }

            // пшеница
            if ($lot->crop_id === 1) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.grain_admixture'),
                    'val' => strval($lot->grain_admixture), // зерновая примесь - 0-100%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.gluten'),
                    'val' => strval($lot->gluten), // клейковина - 12-40%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.protein'),
                    'val' => strval($lot->protein), // протеин - 0-80%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.natural_weight'),
                    'val' => strval($lot->natural_weight), // натура - 50-1000 грам/литр
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.falling_number'),
                    'val' => strval($lot->falling_number), // число падения - 50-500 штук
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.bug'),
                    'val' => strval($lot->bug), // клоп - 0-20%
                ];
            }
            // пшеница твердая
            if ($lot->crop_id === 2) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.grain_admixture'),
                    'val' => strval($lot->grain_admixture), // зерновая примесь - 0-100%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.gluten'),
                    'val' => strval($lot->gluten), // клейковина - 12-40%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.protein'),
                    'val' => strval($lot->protein), // протеин - 0-80%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.natural_weight'),
                    'val' => strval($lot->natural_weight), // натура - 50-1000 грам/литр
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.vitreousness'),
                    'val' => strval($lot->vitreousness), // стекловидность - 20-95%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.bug'),
                    'val' => strval($lot->bug), // клоп - 0-20%
                ];
            }
            // ячмень
            if ($lot->crop_id === 3) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.grain_admixture'),
                    'val' => strval($lot->grain_admixture), // зерновая примесь - 0-100%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.natural_weight'),
                    'val' => strval($lot->natural_weight), // натура - 50-1000 грам/литр
                ];
            }
            // кукуруза
            if ($lot->crop_id === 4) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.ragweed'),
                    'val' => strval($lot->ragweed), // амброзия - 0-500 штук/кг
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.broken'),
                    'val' => strval($lot->broken), // битые - 0-100%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.damaged'),
                    'val' => strval($lot->damaged), // повреждённые - 0-100%
                ];
            }
            // лен
            if ($lot->crop_id === 5) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.oil_content'),
                    'val' => strval($lot->oil_content), // масличность - 0-80%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.peroxide_value'),
                    'val' => strval($lot->peroxide_value), // перекисное число - 0-20%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.acid_value'),
                    'val' => strval($lot->acid_value), // кислотное число - 0-20%
                ];
            }
            // рапс
            if ($lot->crop_id === 6) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.oil_content'),
                    'val' => strval($lot->oil_content), // масличность - 0-80%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.oil_admixture'),
                    'val' => strval($lot->oil_admixture), // масличная примесь - 0-100%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.erucidic_acid'),
                    'val' => strval($lot->erucidic_acid), // эруковая кислота - 0-20%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.peroxide_value'),
                    'val' => strval($lot->peroxide_value), // перекисное число - 0-20%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.acid_value'),
                    'val' => strval($lot->acid_value), // кислотное число - 0-20%
                ];
            }
            // горох
            if ($lot->crop_id === 7) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.broken'),
                    'val' => strval($lot->broken), // битые - 0-100%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.damaged'),
                    'val' => strval($lot->damaged), // повреждённые - 0-100%
                ];
            }
            // соевые бобы
            if ($lot->crop_id === 8) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.protein'),
                    'val' => strval($lot->protein), // протеин - 0-80%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.oil_content'),
                    'val' => strval($lot->oil_content), // масличность - 0-80%
                ];
            }
            // подсолнечник
            if ($lot->crop_id === 9) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.oil_content'),
                    'val' => strval($lot->oil_content), // масличность - 0-80%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.oil_admixture'),
                    'val' => strval($lot->oil_admixture), // масличная примесь - 0-100%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.peroxide_value'),
                    'val' => strval($lot->peroxide_value), // перекисное число - 0-20%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.acid_value'),
                    'val' => strval($lot->acid_value), // кислотное число - 0-20%
                ];
            }
            // нут
            if ($lot->crop_id === 10) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.broken'),
                    'val' => strval($lot->broken), // битые - 0-100%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.dirty'),
                    'val' => strval($lot->dirty), // маранные - 0-100%
                ];
            }
            // рыжик
            if ($lot->crop_id === 11) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.oil_content'),
                    'val' => strval($lot->oil_content), // масличность - 0-80%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.oil_admixture'),
                    'val' => strval($lot->oil_admixture), // масличная примесь - 0-100%
                ];
            }
            // сафлор
            if ($lot->crop_id === 12) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.oil_content'),
                    'val' => strval($lot->oil_content), // масличность - 0-80%
                ];
            }
            // кориандр
            if ($lot->crop_id === 15) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.broken'),
                    'val' => strval($lot->broken), // битые - 0-100%
                ];
            }
            // горчица
            if ($lot->crop_id === 16) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.oil_content'),
                    'val' => strval($lot->oil_content), // масличность - 0-80%
                ];
            }
            // чечевица
            if ($lot->crop_id === 17) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.broken'),
                    'val' => strval($lot->broken), // битые - 0-100%
                ];
            }
            // рожь
            if ($lot->crop_id === 18) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.grain_admixture'),
                    'val' => strval($lot->grain_admixture), // зерновая примесь - 0-100%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.natural_weight'),
                    'val' => strval($lot->natural_weight), // натура - 50-1000 грам/литр
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.falling_number'),
                    'val' => strval($lot->falling_number), // число падения - 50-500 штук
                ];
            }
            // овес
            if ($lot->crop_id === 19) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.grain_admixture'),
                    'val' => strval($lot->grain_admixture), // зерновая примесь - 0-100%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.natural_weight'),
                    'val' => strval($lot->natural_weight), // натура - 50-1000 грам/литр
                ];
            }
            // гречиха
            if ($lot->crop_id === 20) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.grain_admixture'),
                    'val' => strval($lot->grain_admixture), // зерновая примесь - 0-100%
                ];
            }
            // тритикале
            if ($lot->crop_id === 21) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.protein'),
                    'val' => strval($lot->protein), // протеин - 0-80%
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.natural_weight'),
                    'val' => strval($lot->natural_weight), // натура - 50-1000 грам/литр
                ];
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.ash'),
                    'val' => strval($lot->ash), // зольность - 0-100%
                ];
            }
            // рис
            if ($lot->crop_id === 22) {
                $output['lot']['quality'][] = [
                    'name' => Yii::t('app', 'crops.quality.broken'),
                    'val' => strval($lot->broken), // битые - 0-100%
                ];
            }

        }

        return $this->asJson($output);
    }

}
