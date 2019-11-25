<?php
declare(strict_types=1);

namespace app\models\form\messages;

use Yii;
use yii\base\Model;

use app\models\Offer;
use app\models\Messages;

/**
 * Список сообщений
 */
class MessagesList extends Model
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
     * Список сообщений к офферу между пользователями
     * @return array
     */
    public function show() : array
    {
        $output = [];

        if (!$this->validate()) {
            $output['qwe'] = '1';
            return $output;
        }

        // получаем объявление по ссылке
        $offer = Offer::find()->inUser()->byLink($this->link)->communication()->limit(1)->one();
        if (!$offer) {
            $output['qwe'] = '2';
            return $output;
        }

        // список сообщений к офферу
        $messages = Messages::find()->inUser()->byOfferId($offer->id)->orderByCreatedAt()->asArray()->all();

        $user_id = (int) Yii::$app->user->identity->company_id;

        for ($i = 0, $arr = count($messages); $i < $arr; $i++) {
            $output[$i] = [
                'id' => $messages[$i]['id'],
                'sent' => ((int) $messages[$i]['sender_id'] === $user_id) ? true : false,
                'text' => base64_decode($messages[$i]['text']),
                'translation' => base64_decode(strval($messages[$i]['translation'])),
                // сообщения false - прочитано / true - не прочитано
                'unread' => (boolean) $messages[$i]['is_new'],
                'type' => ((int) $messages[$i]['type'] === Messages::TYPE[Messages::TYPE_TEXT])
                    ? Messages::TYPE_TEXT
                    : Messages::TYPE_IMG,
                'created_at' => Yii::$app->formatter->asDatetime($messages[$i]['created_at']),
            ];
        }

        return $output;
    }

}
