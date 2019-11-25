<?php
declare(strict_types=1);

namespace app\models\form\messages;

use Yii;
use yii\base\Model;

use app\models\Offer;
use app\models\Messages;

/**
 * Создание текстового сообщения
 */
class CreateTextMessage extends Model
{
    public $link;   // string - ссылка оффера
    public $text;   // string - текст сообщения



    /**
     * @return array
     */
    public function attributeLabels() : array
    {
        return [
            'link' => 'Ссылка',
            'text' => 'Текст сообщения',
        ];
    }



    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            [
                ['link', 'text'],
                'required',
            ],
            [
                ['link', 'text'],
                'trim',
            ],
        ];
    }



    /**
     * Создаем сообщение
     * @return array
     */
    public function save() : array
    {
        $output = [
            'result' => 'error',
        ];

        if (!$this->validate()) {
            $output['messages'] = 'Возникла ошибка';
            return $output;
        }

        // получаем объявление по ссылке
        $offer = Offer::find()->inUser()->byLink($this->link)->communication()->limit(1)->one();
        if (!$offer) {
            $output['messages'] = 'Возникла ошибка';
            return $output;
        }

        $offer_lot_owner_id = (int) $offer->lot_owner_id;
        $offer_counterparty_id = (int) $offer->counterparty_id;

        // определяем отправителя и получателя
        $sender_id = (int) Yii::$app->user->identity->id;
        $receiver_id = ($sender_id === $offer_lot_owner_id) ? $offer_counterparty_id : $offer_lot_owner_id;

        $message = new Messages();
        $message->offer_id = (int) $offer->id;
        $message->sender_id = $sender_id;
        $message->receiver_id = $receiver_id;
        $message->setTypeText();
        $message->setText($this->text);
        $message->setCreatedAt();

        if (!$message->save()) {
            $output['messages'] = 'Возникла ошибка';
            return $output;
        }

        $output['result'] = 'success';

        return $output;
    }

}
