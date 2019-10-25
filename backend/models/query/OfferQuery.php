<?php
declare(strict_types=1);

namespace app\models\query;

use Yii;
use yii\db\ActiveQuery;

use app\models\Offer;

class OfferQuery extends ActiveQuery
{

    /**
     * Оффер создан(начат) "этим" пользователем, то есть "этот" пользователь
     * подал заявку "твердо" на объявление
     * @return
     */
    public function my()
    {
        return $this->andWhere([
            'counterparty_id' => Yii::$app->user->identity->company_id,
        ]);
    }



    /**
     * Оффер по ID объявления
     * @param  integer $lot_id ID лота
     * @return
     */
    public function byLot($lot_id)
    {
        return $this->andWhere([
            'lot_id' => $lot_id,
        ]);
    }



    /**
     * Оффер по ID контрагента подавшего объявление
     * @return
     */
    public function imOwner()
    {
        return $this->andWhere([
            'lot_owner_id' => Yii::$app->user->identity->company_id,
        ]);
    }



    /**
     * Оффер который ожидает "твердо" или уже в "твердо"
     * @return
     */
    public function auction()
    {
        return $this->andWhere([
            'status' => [
                Offer::STATUS_WAITING,
                Offer::STATUS_AUCTION
            ]
        ]);
    }



    /**
     * Оффер в статусе "твердо"
     * для проверки есть ли оффер в статусе "твердо"
     * @return boolean
     */
    public function hasAuction()
    {
        return $this->andWhere([
            'status' => Offer::STATUS_AUCTION
        ])
        ->exists();
    }



    /**
     * Оффер со статусом аукциона или общения или завершен, с таким статусом
     * объявление удалить нельзя
     * @return
     */
    public function statusLotNotDelete()
    {
        return $this->andWhere([
            'status' => [
                Offer::STATUS_AUCTION,
                Offer::STATUS_COMMUNICATION,
                Offer::STATUS_COMPLETE,
            ]
        ]);
    }

}
