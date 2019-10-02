<?php
declare(strict_types=1);

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

use app\models\query\LotQuery;

use app\models\User;
use app\models\Crops;

/**
 * @property integer   id
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
 * @property string    link             ссылка
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

            [['is_edit','status'], 'boolean'],



            // [['url', 'title'], 'required'],
            // [['url', 'title', 'description', 'keywords', 'text'], 'trim'],
            // [['url', 'title', 'description', 'keywords'], 'string', 'max' => 255],

            // ['url', 'unique'],
            // ['url', 'url'],

            // [['is_edit','status'], 'boolean'],
        ];
    }



    /**
     * @return LotQuery
     */
    public static function find()
    {
        return new LotQuery(get_called_class());
    }



    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id']);
    }

    public function getCrops()
    {
        return $this->hasMany(Crops::className(), ['id' => 'crop_id']);
    }



    /**
     * Устанавливаем ID пользователя
    */
    public function setUserId() : void
    {
        $this->user_id = (int) Yii::$app->user->id;
    }



    /**
     * Устанавливаем ID компании(контрагента)
    */
    public function setCompanyId() : void
    {
        $this->company_id = (int) Yii::$app->user->identity->company_id;
    }



    /**
     * Устанавливаем ID культуры
     * @param integer $crop_id
    */
    public function setCropId(int $crop_id) : void
    {
        /**
         * TODO: проверить наличие ID культуры в БД
         */
        $this->crop_id = abs($crop_id);
    }



    /**
     * Устанавливаем тип объявления (покупка или продажа)
     * @param string $deal
    */
    public function setDeal(string $deal) : void
    {
        $this->deal = $deal;
    }


    /**
     * Устанавливаем цену
     * @param float $price
    */
    public function setPrice(float $price) : void
    {
        $this->price = abs(ceil($price));
    }


    /**
     * Устанавливаем код валюты
     * @param string $currency
    */
    public function setCurrency(string $currency) : void
    {
        $this->currency = $currency;
    }



    /**
     * Устанавливаем объем
     * @param integer $quantity
    */
    public function setQuantity(int $quantity) : void
    {
        $this->quantity = abs($quantity);
    }



    /**
     * Устанавливаем НДС
     * @param integer $vat
    */
    public function setVat(int $vat) : void
    {
        $this->vat = $vat;
    }



    /**
     * Устанавливаем базис поставки
     * @param string $basis
    */
    public function setBasis(string $basis) : void
    {
        $this->basis = $basis;
    }



    /**
     * Устанавливаем базис поставки
     * @param string $fob_port
    */
    public function setBasisFobPort(string $fob_port = '') : void
    {
        $this->fob_port = ($fob_port) ? $fob_port : NULL;
    }



    /**
     * Устанавливаем базис поставки
     * @param string $fob_terminal
    */
    public function setBasisFobTerminal(string $fob_terminal = '') : void
    {
        $this->fob_terminal = ($fob_terminal) ? $fob_terminal : NULL;
    }



    /**
     * Устанавливаем базис поставки
     * @param string $cif_country
    */
    public function setBasisCifCountry(string $cif_country = '') : void
    {
        $this->cif_country = ($cif_country) ? $cif_country : NULL;
    }



    /**
     * Устанавливаем базис поставки
     * @param string $cif_port
    */
    public function setBasisCifPort(string $cif_port = '') : void
    {
        $this->cif_port = ($cif_port) ? $cif_port : NULL;
    }



    /**
     * Устанавливаем текст дополнительной информации
     * @param string $basis
    */
    public function setText(string $text = '') : void
    {
        $this->text = $text;
    }



    /**
     * Устанавливаем статус
    */
    public function setLink() : void
    {
        $this->link = security()->generateRandomString(self::LINK_LENGTH);
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
                $location = $lot->fob_port . ', ' . $lot->fob_terminal;
                break;
            case self::BASIS['CIF']:
                $location = $lot->cif_country . ', ' . $lot->cif_port;
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
                $location = $lot['fob_port'] . ', ' . $lot['fob_terminal'];
                break;
            case self::BASIS['CIF']:
                $location = $lot['cif_country'] . ', ' . $lot['cif_port'];
                break;
            default: break;
        }

        return strval($location);
    }



}
