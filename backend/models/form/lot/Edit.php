<?php
declare(strict_types=1);

namespace app\models\form\lot;

use Yii;
use yii\base\Model;

use app\models\Lot;
use app\models\Crops;
use app\models\Currency;
use app\models\Offer;

/**
 * Редактирование объявления
 */
class Edit extends Model
{
    public $link;            // string

    public $deal;            // string
    public $crop_id;         // int
    public $currency;        // string
    public $price;           // float
    public $quantity;        // int

    public $moisture;        // float | null
    public $foreign_matter;  // float | null
    public $grain_admixture; // float | null
    public $gluten;          // float | null
    public $protein;         // float | null
    public $natural_weight;  // float | null
    public $falling_number;  // float | null
    public $vitreousness;    // float | null
    public $ragweed;         // float | null
    public $bug;             // float | null
    public $oil_content;     // float | null
    public $oil_admixture;   // float | null
    public $broken;          // float | null
    public $damaged;         // float | null
    public $dirty;           // float | null
    public $ash;             // float | null
    public $erucidic_acid;   // float | null
    public $peroxide_value;  // float | null
    public $acid_value;      // float | null
    public $other_color;     // int | null
    public $w;               // int | null

    public $crop_year;       // string | null

    public $basis;           // string
    public $fob_port;        // string | null
    public $fob_terminal;    // string | null
    public $cif_country;     // string | null
    public $cif_port;        // string | null

    public $period;          // string | null
    public $text;            // string | null



    /**
     * @return array
     */
    public function attributeLabels() : array
    {
        return [
            'link' => '',
            'deal' => 'Тип сделки',
            'crop_id' => 'Тип культуры',
            'currency' => 'Валюта',
            'price' => 'Цена',
            'quantity' => 'Объем',
            'moisture' => Yii::t('app', 'crops.quality.moisture'),
            'foreign_matter' => Yii::t('app', 'crops.quality.foreign_matter'),
            'grain_admixture' => Yii::t('app', 'crops.quality.grain_admixture'),
            'gluten' => Yii::t('app', 'crops.quality.gluten'),
            'protein' => Yii::t('app', 'crops.quality.protein'),
            'natural_weight' => Yii::t('app', 'crops.quality.natural_weight'),
            'falling_number' => Yii::t('app', 'crops.quality.falling_number'),
            'vitreousness' => Yii::t('app', 'crops.quality.vitreousness'),
            'ragweed' => Yii::t('app', 'crops.quality.ragweed'),
            'bug' => Yii::t('app', 'crops.quality.bug'),
            'oil_content' => Yii::t('app', 'crops.quality.oil_content'),
            'oil_admixture' => Yii::t('app', 'crops.quality.oil_admixture'),
            'broken' => Yii::t('app', 'crops.quality.broken'),
            'damaged' => Yii::t('app', 'crops.quality.damaged'),
            'dirty' => Yii::t('app', 'crops.quality.dirty'),
            'ash' => Yii::t('app', 'crops.quality.ash'),
            'erucidic_acid' => Yii::t('app', 'crops.quality.erucidic_acid'),
            'peroxide_value' => Yii::t('app', 'crops.quality.peroxide_value'),
            'acid_value' => Yii::t('app', 'crops.quality.acid_value'),
            'other_color' => Yii::t('app', 'crops.quality.other_color'),
            'w' => Yii::t('app', 'crops.quality.w'),
            'crop_year' => Yii::t('app', 'crops.quality.crop_year'),
            'basis' => 'Базис',
            'fob_port' => 'Базис порт',
            'fob_terminal' => 'Базис терминал',
            'cif_country' => 'Базис страна',
            'cif_port' => 'Базис порт',
            'period' => 'Период поставки',
            'text' => 'Дополнительная информация',
        ];
    }



    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            [
                ['link', 'deal', 'crop_id', 'currency', 'price', 'quantity', 'basis', 'period',],
                'required',
            ],
            [
                ['link', 'deal', 'currency', 'crop_year', 'basis', 'fob_port', 'fob_terminal', 'cif_country', 'cif_port', 'period', 'text',],
                'trim',
            ],
            [
                ['moisture', 'foreign_matter', 'grain_admixture', 'gluten', 'protein', 'natural_weight', 'falling_number', 'vitreousness', 'ragweed', 'bug', 'oil_content', 'oil_admixture', 'broken', 'damaged', 'dirty', 'ash', 'erucidic_acid', 'peroxide_value', 'acid_value', 'other_color', 'w', 'crop_year', 'fob_port', 'fob_terminal', 'cif_country', 'cif_port', 'period', 'text',],
                'default',
                'value' => NULL,
            ],
            [
                'deal',
                'in',
                'range' => [Lot::DEAL['buy'], Lot::DEAL['sell'],],
            ],
            [
                'crop_id',
                'in',
                'range' => Crops::find()->select('id')->active()->column(),
            ],
            [
                'currency',
                'in',
                'range' => Currency::find()->select('iso_code_3')->active()->column(),
            ],
            [
                'basis',
                'in',
                'range' => [Lot::BASIS['FOB'], Lot::BASIS['CIF'],],
            ],

            ['price', 'double', 'min' => 1, 'max' => 2147483647, 'message' => 'Некорректное значение цены', 'tooBig' => 'Некорректное значение цены', 'tooSmall' => 'Некорректное значение цены'],

            ['quantity', 'integer', 'min' => 0, 'max' => 2147483647, 'tooBig' => 'Некорректное значение объема', 'tooSmall' => 'Некорректное значение объема'],

            ['moisture', 'double', 'min' => 0, 'max' => 100, 'tooBig' => 'Некорректное значение влажность', 'tooSmall' => 'Некорректное значение влажность'],
            ['foreign_matter', 'double', 'min' => 0, 'max' => 100, 'tooBig' => 'Некорректное значение сорная примесь', 'tooSmall' => 'Некорректное значение сорная примесь'],
            ['grain_admixture', 'double', 'min' => 0, 'max' => 100, 'tooBig' => 'Некорректное значение зерновая примесь', 'tooSmall' => 'Некорректное значение зерновая примесь',],
            ['gluten', 'double', 'min' => 12, 'max' => 40, 'tooBig' => 'Некорректное значение клейковина', 'tooSmall' => 'Некорректное значение клейковина',],
            ['protein', 'double', 'min' => 0, 'max' => 80, 'tooBig' => 'Некорректное значение протеин', 'tooSmall' => 'Некорректное значение протеин',],
            ['natural_weight', 'double', 'min' => 50, 'max' => 1000, 'tooBig' => 'Некорректное значение натура', 'tooSmall' => 'Некорректное значение натура',],
            ['falling_number', 'double', 'min' => 50, 'max' => 500, 'tooBig' => 'Некорректное значение число падения', 'tooSmall' => 'Некорректное значение число падения',],
            ['vitreousness', 'double', 'min' => 20, 'max' => 95, 'tooBig' => 'Некорректное значение стекловидность', 'tooSmall' => 'Некорректное значение стекловидность',],
            ['ragweed', 'double', 'min' => 0, 'max' => 500, 'tooBig' => 'Некорректное значение амброзия', 'tooSmall' => 'Некорректное значение амброзия',],
            ['bug', 'double', 'min' => 0, 'max' => 20, 'tooBig' => 'Некорректное значение клоп', 'tooSmall' => 'Некорректное значение клоп',],
            ['oil_content', 'double', 'min' => 0, 'max' => 80, 'tooBig' => 'Некорректное значение масличность', 'tooSmall' => 'Некорректное значение масличность',],
            ['oil_admixture', 'double', 'min' => 0, 'max' => 100, 'tooBig' => 'Некорректное значение масличная примесь', 'tooSmall' => 'Некорректное значение масличная примесь',],
            ['broken', 'double', 'min' => 0, 'max' => 100, 'tooBig' => 'Некорректное значение битые', 'tooSmall' => 'Некорректное значение битые',],
            ['damaged', 'double', 'min' => 0, 'max' => 100, 'tooBig' => 'Некорректное значение повреждённые', 'tooSmall' => 'Некорректное значение повреждённые',],
            ['dirty', 'double', 'min' => 0, 'max' => 100, 'tooBig' => 'Некорректное значение маранные', 'tooSmall' => 'Некорректное значение маранные',],
            ['ash', 'double', 'min' => 0, 'max' => 100, 'tooBig' => 'Некорректное значение зольность', 'tooSmall' => 'Некорректное значение зольность',],
            ['erucidic_acid', 'double', 'min' => 0, 'max' => 20, 'tooBig' => 'Некорректное значение эруковая кислота', 'tooSmall' => 'Некорректное значение эруковая кислота',],
            ['peroxide_value', 'double', 'min' => 0, 'max' => 20, 'tooBig' => 'Некорректное значение перекисное число', 'tooSmall' => 'Некорректное значение перекисное число',],
            ['acid_value', 'double', 'min' => 0, 'max' => 20, 'tooBig' => 'Некорректное значение кислотное число', 'tooSmall' => 'Некорректное значение кислотное число',],
            ['other_color', 'integer', 'min' => 1, 'max' => 5, 'tooBig' => 'Некорректное значение другой цвет', 'tooSmall' => 'Некорректное значение другой цвет',],
            ['w', 'integer', 'min' => 0, 'max' => 1000, 'tooBig' => 'Некорректное значение w', 'tooSmall' => 'Некорректное значение w',],

            ['crop_year', 'string', 'max' => 100, 'message' => 'Некорректное значение',],

            ['fob_port', 'string', 'max' => 255, 'message' => 'Некорректное значение',],
            ['fob_terminal', 'string', 'max' => 255, 'message' => 'Некорректное значение',],
            ['cif_country', 'string', 'max' => 255, 'message' => 'Некорректное значение',],
            ['cif_port', 'string', 'max' => 255, 'message' => 'Некорректное значение',],

            ['period', 'string', 'max' => 255, 'message' => 'Некорректное значение',],
            ['text', 'string', 'max' => 500, 'message' => 'Некорректное значение',],
        ];
    }



    /**
     * @return boolean
     */
    public function beforeValidate() : bool
    {
        $this->price = tofloat($this->price);

        $this->deal = strtolower($this->deal);
        $this->currency = strtoupper($this->currency);
        $this->basis = strtoupper((string) $this->basis);

        // отчищаем строки от символов отличных от utf-8
        // стираем значения если они не относятся к данному базису
        $this->fob_port = ($this->basis === Lot::BASIS['FOB']) ? mb_convert_encoding($this->fob_port, 'UTF-8', 'UTF-8') : '';
        $this->fob_terminal = ($this->basis === Lot::BASIS['FOB']) ? mb_convert_encoding($this->fob_terminal, 'UTF-8', 'UTF-8') : '';
        $this->cif_country = ($this->basis === Lot::BASIS['CIF']) ? mb_convert_encoding($this->cif_country, 'UTF-8', 'UTF-8') : '';
        $this->cif_port = ($this->basis === Lot::BASIS['CIF']) ? mb_convert_encoding($this->cif_port, 'UTF-8', 'UTF-8') : '';

        // отчищаем строки от символов отличных от utf-8
        $this->crop_year = mb_convert_encoding($this->crop_year, 'UTF-8', 'UTF-8');
        $this->period = mb_convert_encoding($this->period, 'UTF-8', 'UTF-8');
        $this->text = mb_convert_encoding($this->text, 'UTF-8', 'UTF-8');

        /**
         * Ключи - параметры культур
         * Значения - индексы культур в которых используется параметр
         * moisture и foreign_matter не проверяются, так как используются для всех культур
         * @var array
         */
        $arr = [
            'grain_admixture' => ['1','2','3','18','19','20'],
            'gluten' => ['1','2'],
            'protein' => ['1','2','8','21'],
            'natural_weight' => ['1','2','3','18','19','21'],
            'falling_number' => ['1','18'],
            'vitreousness' => ['2'],
            'ragweed' => ['4'],
            'bug' => ['1','2'],
            'oil_content' => ['5','6','8','9','11','12','16'],
            'oil_admixture' => ['6','9','11'],
            'broken' => ['4','7','10','15','17','22'],
            'damaged' => ['4','7'],
            'dirty' => ['10'],
            'ash' => ['21'],
            'erucidic_acid' => ['6'],
            'peroxide_value' => ['5','6','9'],
            'acid_value' => ['5','6','9'],
            'other_color' => ['7'],
            'w' => ['1','2'],
        ];
        $crop_id = (int) $this->crop_id;

        $this->moisture        = (float) $this->moisture;
        $this->foreign_matter  = (float) $this->foreign_matter;
        $this->grain_admixture = (in_array($crop_id, $arr['grain_admixture']))  ? (float) $this->grain_admixture : '';
        $this->gluten          = (in_array($crop_id, $arr['gluten']))           ? (float) $this->gluten : '';
        $this->protein         = (in_array($crop_id, $arr['protein']))          ? (float) $this->protein : '';
        $this->natural_weight  = (in_array($crop_id, $arr['natural_weight']))   ? (float) $this->natural_weight : '';
        $this->falling_number  = (in_array($crop_id, $arr['falling_number']))   ? (float) $this->falling_number : '';
        $this->vitreousness    = (in_array($crop_id, $arr['vitreousness']))     ? (float) $this->vitreousness : '';
        $this->ragweed         = (in_array($crop_id, $arr['ragweed']))          ? (float) $this->ragweed : '';
        $this->bug             = (in_array($crop_id, $arr['bug']))              ? (float) $this->bug : '';
        $this->oil_content     = (in_array($crop_id, $arr['oil_content']))      ? (float) $this->oil_content : '';
        $this->oil_admixture   = (in_array($crop_id, $arr['oil_admixture']))    ? (float) $this->oil_admixture : '';
        $this->broken          = (in_array($crop_id, $arr['broken']))           ? (float) $this->broken : '';
        $this->damaged         = (in_array($crop_id, $arr['damaged']))          ? (float) $this->damaged : '';
        $this->dirty           = (in_array($crop_id, $arr['dirty']))            ? (float) $this->dirty : '';
        $this->ash             = (in_array($crop_id, $arr['ash']))              ? (float) $this->ash : '';
        $this->erucidic_acid   = (in_array($crop_id, $arr['erucidic_acid']))    ? (float) $this->erucidic_acid : '';
        $this->peroxide_value  = (in_array($crop_id, $arr['peroxide_value']))   ? (float) $this->peroxide_value : '';
        $this->acid_value      = (in_array($crop_id, $arr['acid_value']))       ? (float) $this->acid_value : '';
        $this->other_color     = (in_array($crop_id, $arr['other_color']))      ? (int) $this->other_color : '';
        $this->w               = (in_array($crop_id, $arr['w']))                ? (int) $this->w : '';

        return parent::beforeValidate();
    }



    /**
     * Редактирование объявления
     * @return array
     */
    public function save() : array
    {
        $output = [
            'result' => 'error',
        ];

        // получаем объявление по ссылке для редактирования
        $lot = Lot::find()->my()->byLink($this->link)->hasEdit()->limit(1)->one();
        if (!$lot) {
            $output['messages'] = ['Объявление не найдено'];
            return $output;
        }

        // проверка офферов на возможность редактирования объявления
        $offer = Offer::find()->byLot($lot->id)->hasEditLot();
        if ($offer) {
            $output['messages'] = ['Сейчас объявление нельзя редактировать'];
            return $output;
        }

        if (!$this->validate()) {
            $output['messages'] = $this->getFirstErrors();
            return $output;
        }

        Lot::getDb()->transaction(function($db) use ($lot) {
            $lot->crop_id = $this->crop_id;
            $lot->deal = $this->deal;
            $lot->price = $this->price;
            $lot->currency = $this->currency;
            $lot->quantity = $this->quantity;
            $lot->period = $this->period;

            $lot->basis = $this->basis;
            $lot->fob_port = $this->fob_port;
            $lot->fob_terminal = $this->fob_terminal;
            $lot->cif_country = $this->cif_country;
            $lot->cif_port = $this->cif_port;

            $lot->moisture = $this->moisture;
            $lot->foreign_matter = $this->foreign_matter;
            $lot->grain_admixture = $this->grain_admixture;
            $lot->gluten = $this->gluten;
            $lot->protein = $this->protein;
            $lot->natural_weight = $this->natural_weight;
            $lot->falling_number = $this->falling_number;
            $lot->vitreousness = $this->vitreousness;
            $lot->ragweed = $this->ragweed;
            $lot->bug = $this->bug;
            $lot->oil_content = $this->oil_content;
            $lot->oil_admixture = $this->oil_admixture;
            $lot->broken = $this->broken;
            $lot->damaged = $this->damaged;
            $lot->dirty = $this->dirty;
            $lot->ash = $this->ash;
            $lot->erucidic_acid = $this->erucidic_acid;
            $lot->peroxide_value = $this->peroxide_value;
            $lot->acid_value = $this->acid_value;
            $lot->other_color = $this->other_color;
            $lot->w = $this->w;

            $lot->crop_year = $this->crop_year;

            $lot->text = $this->text;

            $lot->save();
        });

        if ($lot->hasErrors()) {
            $output['messages'] = ['При сохранении возникла ошибка, попробуйте позже'];
            return $output;
        }


        $output['result'] = 'success';

        return $output;
    }

}
