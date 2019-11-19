<?php
declare(strict_types=1);

namespace app\models\form\offer;

use Yii;
use yii\base\Model;

use app\models\Offer;

/**
 * Отказ от цены
 */
class CancelPrice extends Model
{
    public $link;   // string - ссылка оффера



    /**
     * @return array
     */
    public function attributeLabels() : array
    {
        return [
            'link' => 'Валюта',
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
     * Отказ от цены.
     * Используется только владельцем объявления, когда вторая сторона предлагает
     * свою цену в третий раз.
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

        Offer::getDb()->transaction(function($db) use ($offer) {
            $offer->setStatusInactive();
            $offer->save();
        });

        if ($offer->hasErrors()) {
            $output['messages'] = 'При сохранении возникла ошибка, попробуйте позже';
            return $output;
        }

        $output['result'] = 'success';

        return $output;
    }

}
