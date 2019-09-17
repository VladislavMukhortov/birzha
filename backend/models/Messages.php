<?php
declare(strict_types=1);

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;

use app\models\User;
use app\models\Company;

/**
 * Клас для работы с личными сообщениями пользователей
 * сообщения хранятся в base64
 *
 * @property integer   id
 * @property integer   sender_id        ID отправителя
 * @property integer   receiver_id      ID получателя
 * @property integer   type             тип контента сообщения
 * @property string    text             текст сообщения
 * @property string    translation      перевод текста сообщения
 * @property boolean   is_new           сообщения false - прочитано / true - не прочитано
 * @property boolean   del_by_sender    удалено отправителем
 * @property boolean   del_by_receiver  удалено получателем
 * @property boolean   notice           статус уведомления
 * @property timestamp created_at       дата создания

 */
class Messages extends ActiveRecord
{

    const DEFAULT_DIALOG_COUNT = 50;    // кол-во диалогов для запроса
    const MESSAGES_COUNT = 40;          // кол-во сообщений для запроса
    const MESSAGE_PREVIEW_LENGTH = 25;  // длина сообщений для превью

    // тип контента сообщения
    const TYPE = [
        'text' => 1,    // текст
        'img' => 2,     // картинка
    ];



    /**
     * @return string
     */
    public static function tableName() : string
    {
        return '{{%messages}}';
    }



    /**
     * @return array
     */
    public function behaviors() : array
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
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
                ['sender_id', 'receiver_id', 'type', 'text'],
                'required'
            ],
            [
                ['sender_id', 'receiver_id'],
                'integer'
            ],
            ['type', 'in', 'range' => [1, 2]],
            [
                ['is_new', 'del_by_sender', 'del_by_receiver', 'notice'],
                'boolean'
            ],
            ['text', 'string'],
            ['translation', 'default', 'value' => NULL],
        ];
    }



    /**
     * Список диалогов пользователя
     * @return array
     */
    public static function getDialogs() : array
    {
        $output = self::_getDialogs();

        for ($i = 0, $arr = count($output); $i < $arr; $i++) {
            $output[$i]['text'] = mb_substr(base64_decode($output[$i]['text']), 0, self::MESSAGE_PREVIEW_LENGTH);

            unset($output[$i]['sender_id']);
            unset($output[$i]['receiver_id']);
        }

        return $output;
    }



    /**
     * Список диалогов пользователя
     * @return array
     */
    private static function _getDialogs() : array
    {
        $output = [];
        $my_id = (int) Yii::$app->user->identity->id;

        $ts = self::tableName();
        $tu = User::tableName();
        $tc = Company::tableName();

        // получаем ID последних сообщений для пользователя
        $last_id_msg = Yii::$app->db->createCommand("
            SELECT MAX(id) AS id
            FROM (
                SELECT id, sender_id, receiver_id FROM {$ts} WHERE sender_id=:id AND del_by_sender=0
                UNION ALL
                SELECT id, receiver_id, sender_id FROM {$ts} WHERE receiver_id=:id AND del_by_receiver=0
            ) t
            GROUP BY sender_id, receiver_id")
            ->bindValue(':id', $my_id)
            ->queryAll();

        if (!$last_id_msg) {
            return $output;
        }

        // переписываем ID в строку
        $last_id_msg_str = '';
        for ($i = 0, $arr = count($last_id_msg); $i < $arr; $i++) {
            $last_id_msg_str .= ',' . (int) $last_id_msg[$i]['id'];
        }
        $last_id_msg_str = trim($last_id_msg_str, ',');

        // получаем информацию о пользователях из диалогов
        $output = Yii::$app->db->createCommand("
            SELECT
                {$tu}.id AS user_id, {$tu}.name AS user_name,
                {$tc}.id AS company_id, {$tc}.name AS company_name,
                {$ts}.id, {$ts}.sender_id, {$ts}.receiver_id, {$ts}.type, {$ts}.text, {$ts}.is_new, {$ts}.created_at
            FROM {$ts}
            RIGHT JOIN {$tu} ON ({$ts}.sender_id = {$tu}.id AND {$ts}.sender_id != :id) OR ({$ts}.receiver_id = {$tu}.id AND {$ts}.receiver_id != :id)
            RIGHT JOIN {$tc} ON {$tu}.company_id = {$tc}.id
            WHERE {$ts}.id IN ({$last_id_msg_str})
            ORDER BY {$ts}.id DESC
            LIMIT :limit")
            ->bindValues([
                ':id' => $my_id,
                ':limit' => self::DEFAULT_DIALOG_COUNT
            ])
            ->queryAll();

        return $output;
    }



    /**
     * Список сообщений между двумя пользователями
     * @param  integer $receiver_id ID получателя
     * @return array
     */
    public static function getMessages(int $receiver_id) : array
    {
        $output = [];
        $messages = self::_getMessages($receiver_id);
        $my_id = (int) Yii::$app->user->identity->id;

        // отмечаем сообщения как прочитанные
        if (count($messages)) {
            self::messagesRead($receiver_id);
        }

        // преобразуем данные и перевернем порядок в массиве
        for ($i = 0, $j = count($messages) - 1; $j >= 0; $i++, --$j) {
            $output[$i] = [
                'id' => $messages[$j]['id'],
                // отправил true-пользователь / false-собеседник
                'sent' => ((int) $messages[$j]['sender_id'] === $my_id) ? true : false,
                'type' => $messages[$j]['type'],
                'text' => base64_decode($messages[$j]['text']),
                'translation' => base64_decode(strval($messages[$j]['translation'])),
                'created_at' => $messages[$j]['created_at'],
            ];
        }

        return $output;
    }



    /**
     * Список сообщений между двумя пользователями
     * @param  integer $receiver_id ID получателя (собеседника)
     * @return array
     */
    private static function _getMessages(int $receiver_id) : array
    {
        $output = [];
        $my_id = (int) Yii::$app->user->identity->id;
        $receiver_id = (int) $receiver_id;

        if (!$receiver_id) {
            return $output;
        }

        $output = (new Query())
            ->select('id, sender_id, receiver_id, type, text, translation, created_at')
            ->from(self::tableName())
            ->where([
                'or',
                ['and', "sender_id={$my_id}", "receiver_id={$receiver_id}", "del_by_sender=0"],
                ['and', "receiver_id={$my_id}", "sender_id={$receiver_id}", "del_by_receiver=0"]
            ])
            ->limit(self::MESSAGES_COUNT)
            ->orderBy(['id' => SORT_DESC])
            ->all();

        return $output;
    }



    /**
     * Список сообщений после $message_id между двумя пользователями
     * @param  integer  $receiver_id ID получателя
     * @param  integer  $message_id  ID сообщения после которого будут выбираться сообщения
     * @return array
     */
    // public static function getLastMessages($receiver_id, $message_id = 0) : array
    // {
    //     return [];
    // }



    /**
     * Сообщения пришедшие от собеседника помечаются как прочитанные
     * @param  integer $receiver_id ID собеседника
     */
    private static function messagesRead(int $receiver_id) : void
    {
        if (!$receiver_id) {
            return;
        }

        static::updateAll(
            [
                'is_new' => false
            ],
            [
                'receiver_id' => Yii::$app->user->identity->id,
                'sender_id' => $receiver_id,
                'is_new' => true
            ]
        );
    }



    /**
     * Удаляем для себя сообщения с собеседником
     * @param  integer  $receiver_id    ID получателя (собеседника)
     * @return boolean                  были ли удалены сообщения
     */
    public static function removeDialog(int $receiver_id) : bool
    {
        if (!$receiver_id) {
            return false;
        }

        $count = static::updateAll(
            [
                'del_by_sender' => new Expression('IF(sender_id=:userId, TRUE, del_by_sender)'),
                'del_by_receiver' => new Expression('IF(receiver_id=:userId, TRUE, del_by_receiver)')
            ],
            [
                'or',
                [
                    'receiver_id' => new Expression(':userId'),
                    'sender_id' => $receiver_id,
                    'del_by_receiver' => false
                ],
                [
                    'sender_id' => new Expression(':userId'),
                    'receiver_id' => $receiver_id,
                    'del_by_sender' => false
                ]
            ],
            [
                'userId' => Yii::$app->user->identity->id
            ]
        );

        return (boolean) $count;
    }



    /**
     * Записываем соообщение в БД
     * @param  integer  $receiver_id    ID получателя (собеседника)
     * @param  string   $text           текст сообщения
     * @return boolean                  статус записи сообщения
     */
    public static function recordMessage(int $receiver_id, $text = '') : bool
    {
        $my_id = (int) Yii::$app->user->identity->id;
        $receiver_id = (int) $receiver_id;

        // отправитель не может быть получателем
        if ($my_id === $receiver_id) {
            return false;
        }

        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

        $message = new Messages();
        $message->sender_id = $my_id;
        $message->receiver_id = $receiver_id;
        $message->type = self::TYPE['text'];
        $message->text = base64_encode($text);

        if ($message->save()) {
            return true;
        }

        return false;
    }



}
