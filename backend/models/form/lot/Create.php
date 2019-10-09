<?php
declare(strict_types=1);

namespace app\models\form\lot;

use Yii;
use yii\base\Model;

use app\models\Lot;
use app\models\Crops;
use app\models\Currency;

/**
 * Редактирование данных пользователя
 */
class Create extends Model
{
    public $deal;            // string
    public $crop_id;         // int
    public $currency;        // string
    public $price;           // float
    public $quantity;        // int

    public $moisture;        // int | null
    public $foreign_matter;  // int | null
    public $grain_admixture; // int | null
    public $gluten;          // int | null
    public $protein;         // int | null
    public $natural_weight;  // int | null
    public $falling_number;  // int | null
    public $vitreousness;    // int | null
    public $ragweed;         // int | null
    public $bug;             // int | null
    public $oil_content;     // int | null
    public $oil_admixture;   // int | null
    public $broken;          // int | null
    public $damaged;         // int | null
    public $dirty;           // int | null
    public $ash;             // int | null
    public $erucidic_acid;   // int | null
    public $peroxide_value;  // int | null
    public $acid_value;      // int | null
    public $other_color;     // int | null

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
            'deal' => '',
            'crop_id' => '',
            'currency' => '',
            'price' => '',
            'quantity' => '',
            'moisture' => '',
            'foreign_matter' => '',
            'grain_admixture' => '',
            'gluten' => '',
            'protein' => '',
            'natural_weight' => '',
            'falling_number' => '',
            'vitreousness' => '',
            'ragweed' => '',
            'bug' => '',
            'oil_content' => '',
            'oil_admixture' => '',
            'broken' => '',
            'damaged' => '',
            'dirty' => '',
            'ash' => '',
            'erucidic_acid' => '',
            'peroxide_value' => '',
            'acid_value' => '',
            'other_color' => '',
            'crop_year' => '',
            'basis' => '',
            'fob_port' => '',
            'fob_terminal' => '',
            'cif_country' => '',
            'cif_port' => '',
            'period' => '',
            'text' => '',
        ];
    }



    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            [
                ['deal', 'crop_id', 'currency', 'price', 'quantity', 'basis',],
                'required',
            ],
            [
                ['deal', 'currency', 'crop_year', 'basis', 'fob_port', 'fob_terminal', 'cif_country', 'cif_port', 'period', 'text',],
                'trim',
            ],
            [
                ['moisture', 'foreign_matter', 'grain_admixture', 'gluten', 'protein', 'natural_weight', 'falling_number', 'vitreousness', 'ragweed', 'bug', 'oil_content', 'oil_admixture', 'broken', 'damaged', 'dirty', 'ash', 'erucidic_acid', 'peroxide_value', 'acid_value', 'other_color', 'crop_year', 'fob_port', 'fob_terminal', 'cif_country', 'cif_port', 'period', 'text',],
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
                'range' => Crops::find()->select('name')->active()->column(),
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

            ['price', 'double', 'min' => 0, 'max' => 2147483647,],

            ['crop_id', 'integer', 'min' => 0,],
            ['quantity', 'integer', 'min' => 0, 'max' => 2147483647,],

            ['moisture', 'integer', 'min' => 0, 'max' => 100, 'message' => 'Некорректное значение',],
            ['foreign_matter', 'integer', 'min' => 0, 'max' => 100, 'message' => 'Некорректное значение',],
            ['grain_admixture', 'integer', 'min' => 0, 'max' => 100, 'message' => 'Некорректное значение',],
            ['gluten', 'integer', 'min' => 12, 'max' => 40, 'message' => 'Некорректное значение',],
            ['protein', 'integer', 'min' => 0, 'max' => 80, 'message' => 'Некорректное значение',],
            ['natural_weight', 'integer', 'min' => 50, 'max' => 1000, 'message' => 'Некорректное значение',],
            ['falling_number', 'integer', 'min' => 50, 'max' => 500, 'message' => 'Некорректное значение',],
            ['vitreousness', 'integer', 'min' => 20, 'max' => 95, 'message' => 'Некорректное значение',],
            ['ragweed', 'integer', 'min' => 0, 'max' => 500, 'message' => 'Некорректное значение',],
            ['bug', 'integer', 'min' => 0, 'max' => 20, 'message' => 'Некорректное значение',],
            ['oil_content', 'integer', 'min' => 0, 'max' => 80, 'message' => 'Некорректное значение',],
            ['oil_admixture', 'integer', 'min' => 0, 'max' => 100, 'message' => 'Некорректное значение',],
            ['broken', 'integer', 'min' => 0, 'max' => 100, 'message' => 'Некорректное значение',],
            ['damaged', 'integer', 'min' => 0, 'max' => 100, 'message' => 'Некорректное значение',],
            ['dirty', 'integer', 'min' => 0, 'max' => 100, 'message' => 'Некорректное значение',],
            ['ash', 'integer', 'min' => 0, 'max' => 100, 'message' => 'Некорректное значение',],
            ['erucidic_acid', 'integer', 'min' => 0, 'max' => 20, 'message' => 'Некорректное значение',],
            ['peroxide_value', 'integer', 'min' => 0, 'max' => 20, 'message' => 'Некорректное значение',],
            ['acid_value', 'integer', 'min' => 0, 'max' => 20, 'message' => 'Некорректное значение',],
            ['other_color', 'integer', 'min' => 1, 'max' => 5, 'message' => 'Некорректное значение',],

            ['crop_year', 'string', 'max' => 100, 'message' => 'Некорректное значение',],

            ['fob_port', 'string', 'max' => 255, 'message' => 'Некорректное значение',],
            ['fob_terminal', 'string', 'max' => 255, 'message' => 'Некорректное значение',],
            ['cif_country', 'string', 'max' => 255, 'message' => 'Некорректное значение',],
            ['cif_port', 'string', 'max' => 255, 'message' => 'Некорректное значение',],

            ['period', 'string', 'max' => 255, 'message' => 'Некорректное значение',],
            ['text', 'string', 'max' => 255, 'message' => 'Некорректное значение',],
        ];
    }



    /**
     * @return boolean
     */
    public function beforeValidate() : bool
    {
        $this->deal = strtolower($this->deal);
        $this->currency = strtoupper($this->currency);
        $this->basis = strtoupper($this->basis);

        // отчищаем строки от символов отличных от utf-8
        // стираем значения если они не относятся к данному базису
        $this->fob_port = ((string) $this->basis === Lot::BASIS['FOB']) ? mb_convert_encoding($this->fob_port, 'UTF-8', 'UTF-8') : '';
        $this->fob_terminal = ((string) $this->basis === Lot::BASIS['FOB']) ? mb_convert_encoding($this->fob_terminal, 'UTF-8', 'UTF-8') : '';
        $this->cif_country = ((string) $this->basis === Lot::BASIS['CIF']) ? mb_convert_encoding($this->cif_country, 'UTF-8', 'UTF-8') : '';
        $this->cif_port = ((string) $this->basis === Lot::BASIS['CIF']) ? mb_convert_encoding($this->cif_port, 'UTF-8', 'UTF-8') : '';

        /**
         * TODO: занулить зачения параметров которые не относятся к конкретной культуре
         */
        /**
         * Ключи - параметры культур
         * Значениях индексы культур в которых используется параметр
         * @var [type]
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
        ];
        $crop_id = (int) $this->crop_id;

        $this->grain_admixture = (in_array($crop_id, $arr['grain_admixture']))  ? $this->grain_admixture : '';
        $this->gluten          = (in_array($crop_id, $arr['gluten']))           ? $this->gluten : '';
        $this->protein         = (in_array($crop_id, $arr['protein']))          ? $this->protein : '';
        $this->natural_weight  = (in_array($crop_id, $arr['natural_weight']))   ? $this->natural_weight : '';
        $this->falling_number  = (in_array($crop_id, $arr['falling_number']))   ? $this->falling_number : '';
        $this->vitreousness    = (in_array($crop_id, $arr['vitreousness']))     ? $this->vitreousness : '';
        $this->ragweed         = (in_array($crop_id, $arr['ragweed']))          ? $this->ragweed : '';
        $this->bug             = (in_array($crop_id, $arr['bug']))              ? $this->bug : '';
        $this->oil_content     = (in_array($crop_id, $arr['oil_content']))      ? $this->oil_content : '';
        $this->oil_admixture   = (in_array($crop_id, $arr['oil_admixture']))    ? $this->oil_admixture : '';
        $this->broken          = (in_array($crop_id, $arr['broken']))           ? $this->broken : '';
        $this->damaged         = (in_array($crop_id, $arr['damaged']))          ? $this->damaged : '';
        $this->dirty           = (in_array($crop_id, $arr['dirty']))            ? $this->dirty : '';
        $this->ash             = (in_array($crop_id, $arr['ash']))              ? $this->ash : '';
        $this->erucidic_acid   = (in_array($crop_id, $arr['erucidic_acid']))    ? $this->erucidic_acid : '';
        $this->peroxide_value  = (in_array($crop_id, $arr['peroxide_value']))   ? $this->peroxide_value : '';
        $this->acid_value      = (in_array($crop_id, $arr['acid_value']))       ? $this->acid_value : '';
        $this->other_color     = (in_array($crop_id, $arr['other_color']))      ? $this->other_color : '';

        return parent::beforeValidate();
    }



    /**
     * Редактирование данных пользователя
     * @return array
     */
    public function save() : array
    {
        $result = [
            'result' => 'error',
        ];

        if (!$this->validate()) {
            $result['messages'] = $this->getFirstErrors();
            return $result;
        }

        $this->crop_year = ($this->crop_year) ? mb_convert_encoding($this->crop_year, 'UTF-8', 'UTF-8') : $this->crop_year;
        $this->period = ($this->period) ? mb_convert_encoding($this->period, 'UTF-8', 'UTF-8') : $this->period;
        $this->text = ($this->text) ? mb_convert_encoding($this->text, 'UTF-8', 'UTF-8') : $this->text;

        $lot = new Lot;
        Lot::getDb()->transaction(function($db) use ($lot) {
            $lot->user_id = (int) Yii::$app->user->id;
            $lot->company_id = (int) Yii::$app->user->identity->company_id;

            $lot->crop_id = $this->crop_id;
            $lot->deal = $this->deal;
            $lot->price = (float) $this->price;
            $lot->currency = $this->currency;
            $lot->quantity = $this->quantity;

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

            $lot->crop_year = $this->crop_year;

            $lot->text = $this->text;
            $lot->setLink();
            $lot->setStatus();
            $lot->save();
        });

        if ($lot->hasErrors()) {
            /**
             * Ошибка валидации - тогда править модель
             * Ошибка сохранения - проблема с доступностью к БД
             */
            $result['messages'] = $this->getFirstErrors();
            return $result;
        }


        $result['result'] = 'success';

        return $result;
    }

}
