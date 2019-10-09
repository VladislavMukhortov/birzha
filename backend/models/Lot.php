<?php
declare(strict_types=1);

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

use app\models\query\LotQuery;

// use app\models\User;
// use app\models\Crops;

/**
 * Таблица объявлений
 *
 * Объявление может быть:
 * - удалено (объявление более никому не доступно)
 * - в архиве (это как черновик или закрытое объявление, доступ только у владельца)
 * - в ожидании (можно указать время когда опубликовать объявление, доступ только у владельца)
 * - активно (размещыется на доске объявлений в ожидании запроса "твердо" или с оффером "твердо")
 * - статус общения (когда стороны договориваются о сделке, на этом этапе не показываем объявление на доске, доступ только для сторон)
 *
 * При переходе оффера в статус "общения" из статуса "твердо" переключаем статус "общение" и на объявлении
 * для того что бы не показывать его на доске. Если стороны не договорились, то переключаем статус на объявлении
 * обратно на статус "активно"
 *
 * Удалить / закрыть объявление можно только из статуса "активно" и "ожидание", если объявление на
 * этапе ОБЩЕНИЯ ТО УТОЧНИТЬ У ШЕФА !!!!!!
 *
 * При удалении или закрытии объявления удаляем все офферы которые подали запрос на "твердо"
 * при действующем запросе "твердо" УТОЧНИТЬ У ШЕФА МОЖНО ЛИ ЗАКРЫВАТЬ ОБЪЯВЛЕНИЕ !!!!
 * Тут же уведомляем вторую сторону что объявление было закрыто
 *
 * Скрываем все данные о владельце до тех пор пока стороны не заключат сделку
 *
 * @property integer   id               ID объявления
 * @property integer   user_id          ID пользователя подавшего объявление
 * @property integer   company_id       ID компании(контрагента) от которой подается объявление
 * @property integer   crop_id          ID культуры к которой пренадлежит объявление
 * @property string    deal             тип объявления buy sell
 * @property float     price            цена
 * @property string    currency         код валюты
 * @property integer   quantity         кол-во тонн (объем)
 * @property string    period           период поставки
 *
 * @property string    basis            базис поставки
 * @property string    fob_port         базис - порт
 * @property string    fob_terminal     базис - терминал
 * @property string    cif_country      базис - страна
 * @property string    cif_port         базис - порт
 *
 * @property integer   moisture         влажность - 0-100%
 * @property integer   foreign_matter   сорная примесь - 0-100%
 * @property integer   grain_admixture  зерновая примесь - 0-100%
 * @property integer   gluten           клейковина - 12-40%
 * @property integer   protein          протеин - 0-80%
 * @property integer   natural_weight   натура - 50-1000 грам/литр
 * @property integer   falling_number   число падения - 50-500 штук
 * @property integer   vitreousness     стекловидность - 20-95%
 * @property integer   ragweed          амброзия - 0-500 штук/кг
 * @property integer   bug              клоп - 0-20%
 * @property integer   oil_content      масличность - 0-80%
 * @property integer   oil_admixture    масличная примесь - 0-100%
 * @property integer   broken           битые - 0-100%
 * @property integer   damaged          повреждённые - 0-100%
 * @property integer   dirty            маранные - 0-100%
 * @property integer   ash              зольность - 0-100%
 * @property integer   erucidic_acid    эруковая кислота - 0-20%
 * @property integer   peroxide_value   перекисное число - 0-20%
 * @property integer   acid_value       кислотное число - 0-20%
 * @property integer   other_color      другой цвет - 1-5%
 * @property string    crop_year        год урожая
 *
 * @property string    text             дополнительная информация (не обязательный параметр)
 * @property string    link             ссылка объявления
 * @property integer   views            кол-во просмотров
 * @property boolean   is_edit          измененный
 * @property integer   status           статус лота
 * @property timestamp created_at       дата создания
 * @property timestamp updated_at       дата изменения
 */
class Lot extends ActiveRecord
{

    /**
     * удаленное объявление
     */
    const STATUS_INACTIVE = 0;

    /**
     * объявление находящийся в архиве
     */
    const STATUS_ARCHIVE = 1;

    /**
     * объявление находящийся в ожидании размещения
     * - например через функцию отложенной публикации (и публикуется в указанное время created_at)
     * - режим черновика
     */
    const STATUS_WAITING = 2;

    /**
     * объявление отображается на доске:
     * - нет запросов оффера
     * - оффер(ы) на стадии подтверждения
     * - оффер со статусом "твердо"
     */
    const STATUS_ACTIVE = 3;

    /**
     * объявление используется в оффере, который находится в статусе переписки двух сторон
     */
    const STATUS_COMMUNICATION = 4;

    /**
     * объявление было закрыто, так как сделка состоялась
     */
    const STATUS_COMPLETE = 5;

    // базис поставки
    const BASIS = [
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

    // тип сделки (покупка или продажа)
    const DEAL = [
        'buy' => 'buy',
        'sell' => 'sell'
    ];

    const LINK_LENGTH = 15;     // длина ссылки по умолчанию
    const LOT_ON_PAGE  = 10;    // кол-во записей на странице



    /**
     * @return string
     */
    public static function tableName() : string
    {
        return '{{%lots}}';
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
                ['user_id','company_id','crop_id','deal','price','currency','quantity','period','basis','link',],
                'required'
            ],
            [
                ['deal','currency','period','basis','fob_port','fob_terminal','cif_country','cif_port','crop_year','text','link',],
                'trim'
            ],
            [
                [
                    'period',
                    'fob_port',
                    'fob_terminal',
                    'cif_country',
                    'cif_port',
                    'moisture',
                    'foreign_matter',
                    'grain_admixture',
                    'gluten',
                    'protein',
                    'natural_weight',
                    'falling_number',
                    'vitreousness',
                    'ragweed',
                    'bug',
                    'oil_content',
                    'oil_admixture',
                    'broken',
                    'damaged',
                    'dirty',
                    'ash',
                    'erucidic_acid',
                    'peroxide_value',
                    'acid_value',
                    'other_color',
                    'crop_year',
                    'text',
                ],
                'default',
                'value' => NULL
            ],

            [['user_id','company_id','crop_id','quantity'], 'integer'],

            ['moisture', 'integer', 'min' => 0, 'max' => 100],
            ['foreign_matter', 'integer', 'min' => 0, 'max' => 100],
            ['grain_admixture', 'integer', 'min' => 0, 'max' => 100],
            ['gluten', 'integer', 'min' => 12, 'max' => 40],
            ['protein', 'integer', 'min' => 0, 'max' => 80],
            ['natural_weight', 'integer', 'min' => 50, 'max' => 1000],
            ['falling_number', 'integer', 'min' => 50, 'max' => 500],
            ['vitreousness', 'integer', 'min' => 20, 'max' => 95],
            ['ragweed', 'integer', 'min' => 0, 'max' => 500],
            ['bug', 'integer', 'min' => 0, 'max' => 20],
            ['oil_content', 'integer', 'min' => 0, 'max' => 80],
            ['oil_admixture', 'integer', 'min' => 0, 'max' => 100],
            ['broken', 'integer', 'min' => 0, 'max' => 100],
            ['damaged', 'integer', 'min' => 0, 'max' => 100],
            ['dirty', 'integer', 'min' => 0, 'max' => 100],
            ['ash', 'integer', 'min' => 0, 'max' => 100],
            ['erucidic_acid', 'integer', 'min' => 0, 'max' => 20],
            ['peroxide_value', 'integer', 'min' => 0, 'max' => 20],
            ['acid_value', 'integer', 'min' => 0, 'max' => 20],
            ['other_color', 'integer', 'min' => 1, 'max' => 5],

            ['link', 'unique'],

            [['is_edit'], 'boolean'],

            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            [
                'status',
                'in',
                'range' => [
                    self::STATUS_INACTIVE,
                    self::STATUS_ARCHIVE,
                    self::STATUS_WAITING,
                    self::STATUS_ACTIVE,
                    self::STATUS_COMMUNICATION,
                    self::STATUS_COMPLETE,
                ]
            ],
        ];
    }



    /**
     * @return LotQuery
     */
    public static function find()
    {
        return new LotQuery(get_called_class());
    }



    // public function getUsers()
    // {
    //     return $this->hasMany(User::className(), ['id' => 'user_id']);
    // }

    // public function getCrops()
    // {
    //     return $this->hasMany(Crops::className(), ['id' => 'crop_id']);
    // }



    /**
     * Устанавливаем ссылку
    */
    public function setLink() : void
    {
        $this->link = Yii::$app->security->generateRandomString(self::LINK_LENGTH);
    }



    /**
     * Устанавливаем статус
    */
    public function setStatus() : void
    {
        $this->status = (int) self::STATUS_ACTIVE;
    }



    /**
     * Возвращаем строку с данными о базисе
     * @param  Lot    $lot объект объявления
     * @return string
     */
    public function getBasisLocation(Lot $lot) : string
    {
        $location = '';

        switch ($lot->basis) {
            case self::BASIS['FOB']:
                $location = sprintf('%s, %s', $lot->fob_port, $lot->fob_terminal);
                break;
            case self::BASIS['CIF']:
                $location = sprintf('%s, %s', $lot->cif_country, $lot->cif_port);
                break;
            default: break;
        }

        return strval($location);
    }



    /**
     * Возвращаем строку с данными о базисе
     * @param  array  $lot объект объявления
     * @return string
     */
    public function getBasisLocationArray($lot = []) : string
    {
        $location = '';

        switch ($lot['basis']) {
            case self::BASIS['FOB']:
                $location = sprintf('%s, %s', $lot['fob_port'], $lot['fob_terminal']);
                break;
            case self::BASIS['CIF']:
                $location = sprintf('%s, %s', $lot['cif_country'], $lot['cif_port']);
                break;
            default: break;
        }

        return strval($location);
    }




    /**
     * Возвращаем строку с данными о качестве культуры из объявления
     * Предполагаем что в данные заполенны как надо
     * @param  array  $lot объект объявления
     * @return string
     */
    public function getStrQualityArray($lot = []) : string
    {
        $quality = '';

        if ($lot['moisture'])        { $quality .= $lot['moisture'] . '%'; }              // влажность - 0-100%
        if ($lot['foreign_matter'])  { $quality .= '/' . $lot['foreign_matter'] . '%'; }  // сорная примесь - 0-100%
        if ($lot['grain_admixture']) { $quality .= '/' . $lot['grain_admixture'] . '%'; } // зерновая примесь - 0-100%
        if ($lot['gluten'])          { $quality .= '/' . $lot['gluten'] . '%'; }          // клейковина - 12-40%
        if ($lot['protein'])         { $quality .= '/' . $lot['protein'] . '%'; }         // протеин - 0-80%
        if ($lot['natural_weight'])  { $quality .= '/' . $lot['natural_weight']; }        // натура - 50-1000 грам/литр
        if ($lot['falling_number'])  { $quality .= '/' . $lot['falling_number']; }        // число падения - 50-500 штук
        if ($lot['vitreousness'])    { $quality .= '/' . $lot['vitreousness'] . '%'; }    // стекловидность - 20-95%
        if ($lot['ragweed'])         { $quality .= '/' . $lot['ragweed']; }               // амброзия - 0-500 штук/кг
        if ($lot['bug'])             { $quality .= '/' . $lot['bug'] . '%'; }             // клоп - 0-20%
        if ($lot['oil_content'])     { $quality .= '/' . $lot['oil_content'] . '%'; }     // масличность - 0-80%
        if ($lot['oil_admixture'])   { $quality .= '/' . $lot['oil_admixture'] . '%'; }   // масличная примесь - 0-100%
        if ($lot['broken'])          { $quality .= '/' . $lot['broken'] . '%'; }          // битые - 0-100%
        if ($lot['damaged'])         { $quality .= '/' . $lot['damaged'] . '%'; }         // повреждённые - 0-100%
        if ($lot['dirty'])           { $quality .= '/' . $lot['dirty'] . '%'; }           // маранные - 0-100%
        if ($lot['ash'])             { $quality .= '/' . $lot['ash'] . '%'; }             // зольность - 0-100%
        if ($lot['erucidic_acid'])   { $quality .= '/' . $lot['erucidic_acid'] . '%'; }   // эруковая кислота - 0-20%
        if ($lot['peroxide_value'])  { $quality .= '/' . $lot['peroxide_value'] . '%'; }  // перекисное число - 0-20%
        if ($lot['acid_value'])      { $quality .= '/' . $lot['acid_value'] . '%'; }      // кислотное число - 0-20%

        return strval($quality);
    }



}
