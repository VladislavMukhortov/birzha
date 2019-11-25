<?php
declare(strict_types=1);

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

use app\models\query\OfferQuery;

/**
 * Таблица сделок (офферы)
 *
 * Оффер может быть:
 * - удален
 * - ожидать "твердо" (вторая сторона запросила "твердо", но это еще не значит что она его получит, зависит от владельца объявления). На одно объявление может приходиться много заявок на "твердо". Подавать заявки можно в любой момент пока объявлние активно (не в статусе переписки и не удаленно)
 * - "твердо" (стороны обдумывают и есть возможность 3 раза предложить свою цену). При соглашении сторон оффер переходит на статус общения сторон (с ценой торга или с начальной ценой), остальные офферы на это объявления удаляются (и их владельцам приходит уведомление). При отказе этот оффер удаляется и владелец объявления может принять другой (если есть).
 * - статус общения (объявление закрывается для всех кроме сторон и присваивается статус общения. Стороны договариваются через приложение о сделке.)
 * - завершено (сделки которые были успешно проведенны через ресурс)
 *
 * Время на "твердо" дает владелец объявления.
 *
 * @property integer   id               ID оффера
 * @property integer   lot_id           ID объявления
 * @property integer   lot_owner_id     ID компании которая подала объявление покупки/продажи
 * @property integer   counterparty_id  ID компании(второй стороны) которая проявила интерес к объявлению
 * @property float     require_price_1  первая цена оффера от второй стороны
 * @property float     require_price_2  вторая цена оффера от второй стороны
 * @property float     require_price_3  третья цена оффера от второй стороны
 * @property float     lot_price_1      ответ на первую цену от второй стороны
 * @property float     lot_price_2      ответ на вторую цену от второй стороны
 * @property integer   auction_time_s   время в секундах на которое дается "твердо"
 * @property string    link             ссылка
 * @property integer   status           статус офера
 * @property timestamp created_at       дата создания
 * @property timestamp updated_at       дата изменения
 * @property timestamp ended_at         время окончания "твердо" между пользователями
 */
class Offer extends ActiveRecord
{

    /**
     * время на которое дается статус "твердо"
     * ключи - кол-во минут
     */
    const AUCTION_TIME = [
        '30' => 1800,   // 30 минут
        '60' => 3600,   // 60 минут
        '120' => 7200,  // 120 минут
    ];
    // время которое дается статус "твердо" по умолчанию
    const DEFAULT_AUCTION_TIME = 30;

    /**
     * не активный (завершенный - стороны не договорились или устекло вермя) или удаленный оффер
     */
    const STATUS_INACTIVE = 0;

    /**
     * оффер находящийся в архиве
     * или когда оффер был заключен с другой стороной
     * !! НЕ ИСПОЛЬЗОВАТЬ
     */
    const STATUS_ARCHIVE = 1;

    /**
     * оффер находящийся в ожидании пока владелец лота не примет (даст статус "твердо")
     * или не отклонит (статус "удален")
     */
    const STATUS_WAITING = 2;

    /**
     * оффер в статусе "твердо" то есть ведется аукцион можду двумя сторонами
     * - при соглашении сторон оффер переходит на следующий этап (переписка)
     * - в ином случае удаляется
     */
    const STATUS_AUCTION = 3;

    /**
     * оффер в статусе переписки двух сторон
     */
    const STATUS_COMMUNICATION = 4;

    /**
     * статус законченой сделки
     */
    const STATUS_COMPLETE = 5;

    const LINK_LENGTH = 15;     // длина ссылки по умолчанию

    const OFFER_ON_PAGE  = 10;  // кол-во записей на странице



    /**
     * @return string
     */
    public static function tableName() : string
    {
        return '{{%offers}}';
    }



    /**
     * @return array
     */
    public function behaviors() : array
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('UTC_TIMESTAMP()')
            ]
        ];
    }



    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            [
                ['lot_id', 'lot_owner_id', 'counterparty_id', 'link',],
                'required'
            ],
            [
                ['link',],
                'trim'
            ],
            [
                [
                    'require_price_1',
                    'require_price_2',
                    'require_price_3',
                    'lot_price_1',
                    'lot_price_2',
                    'auction_time_s',
                    'ended_at',
                ],
                'default',
                'value' => NULL
            ],
            [
                ['lot_id', 'lot_owner_id', 'counterparty_id',],
                'integer'
            ],
            ['link', 'unique'],
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            [
                'status',
                'in',
                'range' => [
                    self::STATUS_INACTIVE,
                    self::STATUS_ARCHIVE,
                    self::STATUS_WAITING,
                    self::STATUS_AUCTION,
                    self::STATUS_COMMUNICATION,
                    self::STATUS_COMPLETE,
                ]
            ]
        ];
    }


    /**
     * @return OfferQuery
     */
    public static function find()
    {
        return new OfferQuery(get_called_class());
    }



    /**
     * Устанавливаем статус
    */
    public function setLink() : void
    {
        $this->link = Yii::$app->security->generateRandomString(self::LINK_LENGTH);
    }



    /**
     * Устанавливаем статус оффера как удаленный
     * Когда стороны не договорились о цене
     * Либо закончилось время для "твердо"
    */
    public function setStatusInactive() : void
    {
        $this->status = self::STATUS_INACTIVE;
    }



    /**
     * Устанавливаем статус ожидание подтверждения статуса "твердо"
    */
    public function setStatusWaiting() : void
    {
        $this->status = self::STATUS_WAITING;
    }



    /**
     * Устанавливаем статус "твердо"
    */
    public function setStatusAuction() : void
    {
        $this->status = self::STATUS_AUCTION;
    }



    /**
     * Устанавливаем статус коммуникации
     * Этот статус следует за статусом "твердо",
     * когда стороны согласны к дальнейшиму сотрудничеству
    */
    public function setStatusCommunication() : void
    {
        $this->status = self::STATUS_COMMUNICATION;
    }



    /**
     * Устанавливаем время окончания статуса "твердо" для оффера
     * @param string $key ключ из массива AUCTION_TIME
     */
    public function setEndedAt($key) : void
    {
        if (!array_key_exists($key, self::AUCTION_TIME)) {
            $key = Offer::DEFAULT_AUCTION_TIME;
        }

        $auction_time_s = self::AUCTION_TIME[$key];
        $time = time() + $auction_time_s;
        $this->auction_time_s = $auction_time_s;
        /**
         * Не используем formatter->asDatetime, так как после авторизации пользователя
         * он возвращает время в часовом поясе пользователя
         */
        $this->ended_at = date_format(date_create("@{$time}"), Yii::$app->params['db.commonDatetime']);
    }



    /**
     * Определяем кто из пользователей предлагает цену.
     * Контрагент имеет право 3 раза предложить свою цену. Владелец либо соглашается,
     * либо указывает цену на которую он готов
     *
     * 'owner'         - цену вводит владелец объявления
     * 'counterparty'  - цену вводит вторая сторона
     * 'last'          - последний шаг, можно либо согласиться, либо отказаться
     *
     * @return string
     */
    public function priceOfferInAuction() : string
    {
        $str = 'counterparty';

        $require_price_1 = (float) $this->require_price_1;
        $require_price_2 = (float) $this->require_price_2;
        $require_price_3 = (float) $this->require_price_3;
        $lot_price_1 = (float) $this->lot_price_1;
        $lot_price_2 = (float) $this->lot_price_2;

        if ($require_price_1) {
            $str = 'owner';
        }

        if ($lot_price_1 && !$require_price_2) {
            $str = 'counterparty';
        }

        if ($require_price_2 && !$lot_price_2) {
            $str = 'owner';
        }

        if ($lot_price_2 && !$require_price_3) {
            $str = 'counterparty';
        }

        // if ($require_price_1 || $require_price_2) {
        //     $str = 'owner';
        // }

        // if ($lot_price_1 || $lot_price_2) {
        //     $str = 'counterparty';
        // }

        if ($require_price_3) {
            $str = 'last';
        }
        return $str;
    }



    /**
     * [priceListInAuction description]
     * @param  string $currency буквенный код валюты
     * @return array
     */
    public function priceListInAuction($currency) : array
    {
        $price = [
            'require_1' => '',
            'require_2' => '',
            'require_3' => '',
            'lot_1' => '',
            'lot_2' => '',
        ];

        $is_lot_owner = (int) $this->lot_owner_id === Yii::$app->user->identity->company_id;

        if ((float) $this->require_price_1) {
            $require_1 = Yii::$app->formatter->asCurrency((float) $this->require_price_1, $currency);
            $price['require_1'] = ($is_lot_owner)
                ? sprintf('%s: %s', 'Контрагент запросил цену', $require_1)
                : sprintf('%s: %s', 'Вы запросили цену', $require_1);
        }
        if ((float) $this->require_price_2) {
            $require_2 = Yii::$app->formatter->asCurrency((float) $this->require_price_2, $currency);
            $price['require_2'] = ($is_lot_owner)
                ? sprintf('%s: %s', 'Контрагент запросил цену', $require_2)
                : sprintf('%s: %s', 'Вы запросили цену', $require_2);
        }
        if ((float) $this->require_price_3) {
            $require_3 = Yii::$app->formatter->asCurrency((float) $this->require_price_3, $currency);
            $price['require_3'] = ($is_lot_owner)
                ? sprintf('%s: %s', 'Контрагент запросил цену', $require_3)
                : sprintf('%s: %s', 'Вы запросили цену', $require_3);
        }
        if ((float) $this->lot_price_1) {
            $lot_1 = Yii::$app->formatter->asCurrency((float) $this->lot_price_1, $currency);
            $price['lot_1'] = ($is_lot_owner)
                ? sprintf('%s: %s', 'Вы установили цену', $lot_1)
                : sprintf('%s: %s', 'Владелец установил цену', $lot_1);
        }
        if ((float) $this->lot_price_2) {
            $lot_2 = Yii::$app->formatter->asCurrency((float) $this->lot_price_2, $currency);
            $price['lot_2'] = ($is_lot_owner)
                ? sprintf('%s: %s', 'Вы установили цену', $lot_2)
                : sprintf('%s: %s', 'Владелец установил цену', $lot_2);
        }

        return $price;
    }



    /**
     * Устанавливаем цену
     * @param float $price новая цена оффера, устанавливается во время торгов (ТВЕРДО)
     */
    public function setPrice($price) : void
    {
        // Цену предлагает контрагент (вторая сторона)
        if ((int) $this->counterparty_id === Yii::$app->user->identity->company_id) {
            if (!$this->require_price_1) {
                $this->require_price_1 = (float) $price;
                return;
            }
            if ($this->lot_price_1 && !$this->require_price_2) {
                $this->require_price_2 = (float) $price;
                return;
            }
            if ($this->lot_price_2 && !$this->require_price_3) {
                $this->require_price_3 = (float) $price;
                return;
            }
        }

        // Цену устанавливает владелец объявления
        if ((int) $this->lot_owner_id === Yii::$app->user->identity->company_id) {
            if ($this->require_price_1 && !$this->lot_price_1) {
                $this->lot_price_1 = (float) $price;
                return;
            }
            if ($this->require_price_2 && !$this->lot_price_2) {
                $this->lot_price_2 = (float) $price;
                return;
            }
        }
    }

}
