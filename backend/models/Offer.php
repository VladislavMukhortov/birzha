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
        $this->ended_at = Yii::$app->formatter->asDatetime($time, Yii::$app->params['db.commonDatetime']);
    }

}
