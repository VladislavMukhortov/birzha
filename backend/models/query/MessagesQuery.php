<?php
declare(strict_types=1);

namespace app\models\query;

use Yii;
use yii\db\ActiveQuery;

use app\models\Messages;

class MessagesQuery extends ActiveQuery
{

    /**
     * Проверяем пользователя
     * @return
     */
    public function inUser()
    {
        return $this->andWhere([
            'or',
            ['sender_id' => Yii::$app->user->identity->company_id],
            ['receiver_id' => Yii::$app->user->identity->company_id]
        ]);
    }



    /**
     * Выборка сообщений только по ID оффера
     * @param  integer $id ID оффера
     * @return
     */
    public function byOfferId($id)
    {
        return $this->andWhere([
            'offer_id' => $id
        ]);
    }



    /**
     * Сортируем по дате создания в порядке уменьшения
     * @return
     */
    public function orderByCreatedAt()
    {
        return $this->orderBy([
            'created_at' => SORT_ASC,
            'id' => SORT_ASC,
        ]);
    }

}
