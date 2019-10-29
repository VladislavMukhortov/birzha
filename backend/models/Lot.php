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
 * @property float     moisture         влажность - 0-100%
 * @property float     foreign_matter   сорная примесь - 0-100%
 * @property float     grain_admixture  зерновая примесь - 0-100%
 * @property float     gluten           клейковина - 12-40%
 * @property float     protein          протеин - 0-80%
 * @property float     natural_weight   натура - 50-1000 грам/литр
 * @property float     falling_number   число падения - 50-500 штук
 * @property float     vitreousness     стекловидность - 20-95%
 * @property float     ragweed          амброзия - 0-500 штук/кг
 * @property float     bug              клоп - 0-20%
 * @property float     oil_content      масличность - 0-80%
 * @property float     oil_admixture    масличная примесь - 0-100%
 * @property float     broken           битые - 0-100%
 * @property float     damaged          повреждённые - 0-100%
 * @property float     dirty            маранные - 0-100%
 * @property float     ash              зольность - 0-100%
 * @property float     erucidic_acid    эруковая кислота - 0-20%
 * @property float     peroxide_value   перекисное число - 0-20%
 * @property float     acid_value       кислотное число - 0-20%
 * @property integer   other_color      другой цвет - 1-5
 * @property integer   w                w - 0 - 1000w
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
                ['period','fob_port','fob_terminal','cif_country','cif_port','moisture','foreign_matter','grain_admixture','gluten','protein','natural_weight','falling_number','vitreousness','ragweed','bug','oil_content','oil_admixture','broken','damaged','dirty','ash','erucidic_acid','peroxide_value','acid_value','other_color','w','crop_year','text',],
                'default',
                'value' => NULL
            ],

            [['user_id','company_id','crop_id'], 'integer'],

            ['price', 'double', 'min' => 0, 'max' => 2147483647],
            ['quantity', 'integer', 'min' => 0, 'max' => 2147483647],

            ['moisture', 'double', 'min' => 0, 'max' => 100],
            ['foreign_matter', 'double', 'min' => 0, 'max' => 100],
            ['grain_admixture', 'double', 'min' => 0, 'max' => 100],
            ['gluten', 'double', 'min' => 12, 'max' => 40],
            ['protein', 'double', 'min' => 0, 'max' => 80],
            ['natural_weight', 'double', 'min' => 50, 'max' => 1000],
            ['falling_number', 'double', 'min' => 50, 'max' => 500],
            ['vitreousness', 'double', 'min' => 20, 'max' => 95],
            ['ragweed', 'double', 'min' => 0, 'max' => 500],
            ['bug', 'double', 'min' => 0, 'max' => 20],
            ['oil_content', 'double', 'min' => 0, 'max' => 80],
            ['oil_admixture', 'double', 'min' => 0, 'max' => 100],
            ['broken', 'double', 'min' => 0, 'max' => 100],
            ['damaged', 'double', 'min' => 0, 'max' => 100],
            ['dirty', 'double', 'min' => 0, 'max' => 100],
            ['ash', 'double', 'min' => 0, 'max' => 100],
            ['erucidic_acid', 'double', 'min' => 0, 'max' => 20],
            ['peroxide_value', 'double', 'min' => 0, 'max' => 20],
            ['acid_value', 'double', 'min' => 0, 'max' => 20],
            ['other_color', 'integer', 'min' => 1, 'max' => 5],
            ['w', 'integer', 'min' => 0, 'max' => 1000],

            ['link', 'unique'],

            ['is_edit', 'boolean'],

            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            [
                'status',
                'in',
                'range' => [self::STATUS_INACTIVE,self::STATUS_ARCHIVE,self::STATUS_WAITING,self::STATUS_ACTIVE,self::STATUS_COMMUNICATION,self::STATUS_COMPLETE]
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
     * Устанавливаем статус по умолчанию - активно и размещается на доске
    */
    public function setStatus() : void
    {
        $this->status = (int) self::STATUS_ACTIVE;
    }



    /**
     * Устанавливаем статус - удалено
    */
    public function setStatusDelete() : void
    {
        $this->status = (int) self::STATUS_ARCHIVE;
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
     * @param  array  $l объект объявления. Должно быть переданны все параметры и ID культуры
     * @return string
     */
    public function getStrQuality($l = []) : string
    {
        $quality = '';
        $crop_id = isset($l['crop_id']) ? (int) $l['crop_id'] : 0;

        switch ($crop_id) {
            case 1:
                // пшеница - протеин / влага / натура / клейковина / число падения / W / клоп / зерновая примесь / сорная примесь
                $quality = sprintf('%G/%G/%G/%G/%G/%Gw/%G/%G/%G', $l['protein'], $l['moisture'], $l['natural_weight'], $l['gluten'], $l['falling_number'], $l['w'], $l['bug'], $l['grain_admixture'], $l['foreign_matter']);
                break;
            case 2:
                // пшеница твердая - протеин / влага / натура / клейковина / клоп / зерновая примесь / сорная примесь / стекловидность
                $quality = sprintf('%G/%G/%G/%G/%Gw/%G/%G/%G/%G', $l['protein'], $l['moisture'], $l['natural_weight'], $l['gluten'], $l['w'], $l['bug'], $l['grain_admixture'], $l['foreign_matter'], $l['vitreousness']);
                break;
            case 3:
                // ячмень - влага / натура / зерновая примесь / сорная примесь
                $quality = sprintf('%G/%G/%G/%G', $l['moisture'], $l['natural_weight'], $l['grain_admixture'], $l['foreign_matter']);
                break;
            case 4:
                // кукуруза - влага / битые / поврежденые / амброзия / сорная примесь
                $quality = sprintf('%G/%G/%G/%G/%G', $l['moisture'], $l['broken'], $l['damaged'], $l['ragweed'], $l['foreign_matter']);
                break;
            case 5:
                // лен - масличность / влага / сорная примесь / кислотное число / перекисное число
                $quality = sprintf('%G/%G/%G/%G/%G', $l['oil_content'], $l['moisture'], $l['foreign_matter'], $l['acid_value'], $l['peroxide_value']);
                break;
            case 6:
                // рапс - масличность / масличная примесь / влага / сорная примесь / кислотное число / перекисное число / эруковая кислота
                $quality = sprintf('%G/%G/%G/%G/%G/%G/%G', $l['oil_content'], $l['oil_admixture'], $l['moisture'], $l['foreign_matter'], $l['acid_value'], $l['peroxide_value'], $l['erucidic_acid']);
                break;
            case 7:
                // горох - влага / битые / повреждённые / другой цвет / сорная примесь
                $quality = sprintf('%G/%G/%G/%G/%G', $l['moisture'], $l['broken'], $l['damaged'], $l['other_color'], $l['foreign_matter']);
                break;
            case 8:
                // соевые бобы - протеин / влага / сорная примесь / масличность
                $quality = sprintf('%G/%G/%G/%G', $l['protein'], $l['moisture'], $l['foreign_matter'], $l['oil_content']);
                break;
            case 9:
                // подсолнечник - масличность / масличная примесь / влажность / сорная примесь / кислотное число / перекисное число
                $quality = sprintf('%G/%G/%G/%G/%G/%G', $l['oil_content'], $l['oil_admixture'], $l['moisture'], $l['foreign_matter'], $l['acid_value'], $l['peroxide_value']);
                break;
            case 10:
                // нут - влага / битые / повреждённые / маранные / сорная примесь
                $quality = sprintf('%G/%G/%G/%G', $l['moisture'], $l['broken'], $l['dirty'], $l['foreign_matter']);
                break;
            case 11:
                // рыжик - масличность / масличная примесь / влажность /сорная примесь
                $quality = sprintf('%G/%G/%G/%G', $l['oil_content'], $l['oil_admixture'], $l['moisture'], $l['foreign_matter']);
                break;
            case 12:
                // сафлор - масличность / влажность /сорная примесь
                $quality = sprintf('%G/%G/%G', $l['oil_content'], $l['moisture'], $l['foreign_matter']);
                break;
            case 13:
                // сорго - влажность / сорная примесь
                $quality = sprintf('%G/%G', $l['moisture'], $l['foreign_matter']);
                break;
            case 14:
                // просо - влажность / сорная примесь
                $quality = sprintf('%G/%G', $l['moisture'], $l['foreign_matter']);
                break;
            case 15:
                // кориандр - влажность / битые / сорная примесь
                $quality = sprintf('%G/%G/%G', $l['moisture'], $l['broken'], $l['foreign_matter']);
                break;
            case 16:
                // горчица - масличность / влажность / сорная примесь
                $quality = sprintf('%G/%G/%G', $l['oil_content'], $l['moisture'], $l['foreign_matter']);
                break;
            case 17:
                // чечевица - влага / битые / сорная примесь
                $quality = sprintf('%G/%G/%G', $l['moisture'], $l['broken'], $l['foreign_matter']);
                break;
            case 18:
                // рожь - влажность / натура / число падения / зерновая примесь / сорная примесь
                $quality = sprintf('%G/%G/%G/%G/%G', $l['moisture'], $l['natural_weight'], $l['falling_number'], $l['grain_admixture'], $l['foreign_matter']);
                break;
            case 19:
                // овес - влажность / натура / зерновая примесь / сорная примесь
                $quality = sprintf('%G/%G/%G/%G', $l['moisture'], $l['natural_weight'], $l['grain_admixture'], $l['foreign_matter']);
                break;
            case 20:
                // гречиха - влажность / зерновая примесь / сорная примесь
                $quality = sprintf('%G/%G/%G', $l['moisture'], $l['grain_admixture'], $l['foreign_matter']);
                break;
            case 21:
                // тритикале - влажность / протеин / натура / зольность / сорная примесь
                $quality = sprintf('%G/%G/%G/%G/%G', $l['moisture'], $l['protein'], $l['natural_weight'], $l['ash'], $l['foreign_matter']);
                break;
            case 22:
                // рис - влажность / битые / сорная примесь
                $quality = sprintf('%G/%G/%G', $l['moisture'], $l['broken'], $l['foreign_matter']);
                break;
            default:
                break;
        }

        return $quality;
    }



}
