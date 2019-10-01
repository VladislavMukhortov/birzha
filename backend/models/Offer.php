<?php
declare(strict_types=1);

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

use app\models\query\OfferQuery;

/**
 * @property integer   id               ID оффера
 * @property integer   lot_id           ID объявления
 * @property integer   company_id       ID компании (второй стороны)
 * @property float     require_price_1  первая цена оффера от второй стороны
 * @property float     require_price_2  вторая цена оффера от второй стороны
 * @property float     require_price_3  третья цена оффера от второй стороны
 * @property float     lot_price_1      ответ на первую цену от второй стороны
 * @property float     lot_price_2      ответ на вторую цену от второй стороны
 * @property integer   auction_time_s   время в секундах на которое дается "твердо"
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

    /**
     * не активный (завершенный - стороны не договорились или устекло вермя) или удаленный оффер
     */
    const STATUS_INACTIVE = 0;

    /**
     * оффер находящийся в архиве
     * или когда оффер был заключен с другой стороной
     * выбрать или оставить так
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
                ['lot_id','company_id',],
                'required'
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
                ['lot_id','company_id',],
                'integer'
            ],
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
    public function setStatusWaiting() : void
    {
        $this->status = self::STATUS_WAITING;
    }

}
