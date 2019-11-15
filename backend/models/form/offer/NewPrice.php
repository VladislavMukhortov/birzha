<?php
declare(strict_types=1);

namespace app\models\form\offer;

use Yii;
use yii\base\Model;

use app\models\Offer;
use app\models\Lot;

/**
 * Редактирование данных пользователя
 */
class NewPrice extends Model
{
    public $link;   // string - ссылка оффера
    public $price;  // float  - новая цена



    /**
     * @return array
     */
    public function attributeLabels() : array
    {
        return [
            'link' => 'Валюта',
            'price' => 'Цена',
        ];
    }



    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            [
                ['link', 'price',],
                'required',
            ],
            [
                ['link', 'price',],
                'trim',
            ],
            [
                'price',
                'double',
                'min' => 0,
                'max' => 2147483647,
                'message' => 'Некорректное значение',
                'tooBig' => 'Некорректное значение цены',
                'tooSmall' => 'Некорректное значение цены'
            ],
        ];
    }



    /**
     * @return boolean
     */
    public function beforeValidate() : bool
    {
        $this->price = tofloat($this->price);
        return parent::beforeValidate();
    }



    /**
     * Редактирование данных пользователя
     * @return array
     */
    public function save() : array
    {
        $output = [
            'result' => 'error',
        ];

        if (!$this->validate()) {
            $output['messages'] = $this->getFirstErrors();
            return $output;
        }

        // получаем объявление по ссылке для редактирования
        $offer = Offer::find()->inUser()->byLink($this->link)->auction()->limit(1)->one();
        if (!$offer) {
            $output['messages'] = ['Объявление не найдено'];
            return $output;
        }

        Offer::getDb()->transaction(function($db) use ($offer) {
            $offer->setPrice($this->price);
            $offer->save();
        });

        if ($offer->hasErrors()) {
            $output['messages'] = ['При сохранении возникла ошибка, попробуйте позже'];
            return $output;
        }

        $output['result'] = 'success';

        $require_price_1 = (float) $offer->require_price_1;
        $require_price_2 = (float) $offer->require_price_2;
        $require_price_3 = (float) $offer->require_price_3;
        $lot_price_1 = (float) $offer->lot_price_1;
        $lot_price_2 = (float) $offer->lot_price_2;

        $lot = Lot::find()->byId($offer->lot_id)->active()->limit(1)->one();

        $output['offer']['price'] = [
            'require_1' => ($require_price_1) ? Yii::$app->formatter->asCurrency($require_price_1, $lot->currency) : '',
            'require_2' => ($require_price_2) ? Yii::$app->formatter->asCurrency($require_price_2, $lot->currency) : '',
            'require_3' => ($require_price_3) ? Yii::$app->formatter->asCurrency($require_price_3, $lot->currency) : '',
            'lot_1' => ($lot_price_1) ? Yii::$app->formatter->asCurrency($lot_price_1, $lot->currency) : '',
            'lot_2' => ($lot_price_2) ? Yii::$app->formatter->asCurrency($lot_price_2, $lot->currency) : '',
        ];
        $output['offer']['price_offer'] = $offer->priceOfferInAuction();

        return $output;
    }

}
