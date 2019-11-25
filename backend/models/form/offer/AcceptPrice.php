<?php
declare(strict_types=1);

namespace app\models\form\offer;

use Yii;
use yii\base\Model;

use app\models\Offer;
use app\models\Lot;

/**
 * Принимаем цену которую торговали в твердо
 */
class AcceptPrice extends Model
{
    public $link;   // string - ссылка оффера



    /**
     * @return array
     */
    public function attributeLabels() : array
    {
        return [
            'link' => 'Ссылка',
        ];
    }



    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            ['link', 'required',],
            ['link', 'trim',],
        ];
    }



    /**
     * Принимаем цену которую торговали в твердо
     * @return array
     */
    public function save() : array
    {
        $output = [
            'result' => 'error',
        ];

        if (!$this->validate()) {
            $output['messages'] = 'Объявление не найдено';
            return $output;
        }

        // получаем объявление по ссылке для редактирования
        $offer = Offer::find()->inUser()->byLink($this->link)->auction()->limit(1)->one();
        if (!$offer) {
            $output['messages'] = 'Объявление не найдено';
            return $output;
        }

        $lot = Lot::find()->byId($offer->lot_id)->active()->limit(1)->one();
        if (!$lot) {
            $output['messages'] = 'Объявление не найдено';
            return $output;
        }

        $transaction_offer = Offer::getDb()->beginTransaction();
        $transaction_lot = Lot::getDb()->beginTransaction();

        // определяем цену
        $price = $lot->price;
        if ($offer->require_price_1) {
            $price = $offer->require_price_1;
        }
        if ($offer->lot_price_1) {
            $price = $offer->lot_price_1;
        }
        if ($offer->require_price_2) {
            $price = $offer->require_price_2;
        }
        if ($offer->lot_price_2) {
            $price = $offer->lot_price_2;
        }
        if ($offer->require_price_3) {
            $price = $offer->require_price_3;
        }

        try {
            $offer->setStatusCommunication();

            $lot->setStatusCommunication();
            $lot->price = (float) $price;

            if ($offer->save() && $lot->save()) {
                $transaction_offer->commit();
                $transaction_lot->commit();
            }
        } catch(\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        if ($offer->hasErrors() || $lot->hasErrors()) {
            $output['messages'] = 'При сохранении возникла ошибка, попробуйте позже';
            return $output;
        }

        $output['result'] = 'success';

        return $output;
    }

}
