<?php
declare(strict_types=1);

namespace app\models\form\lot;

use Yii;
use yii\base\Model;

use app\models\Lot;
use app\models\Offer;

/**
 * Удаление объявления
 */
class Delete extends Model
{
    public $link;   // ссылка на объявление



    /**
     * @return array
     */
    // public function attributeLabels() : array
    // {
    //     return [
    //         'link' => '',
    //     ];
    // }



    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            ['link', 'required',],
            ['link', 'trim',],
            ['link', 'string', 'max' => 255, 'message' => 'Некорректное значение', 'tooLong' => 'Некорректное значение', 'tooShort' => 'Некорректное значение',],
        ];
    }



    /**
     * Удаляем объявление
     * Можно удалить пока объявление в свободном доступе,
     * когда объявление в статусе "твердо" или на этапе общения
     * или в каком либо другом статусе взаимодействует с другим пользоавтелем
     *
     * @return array
     */
    public function delete() : array
    {
        $output = [
            'result' => 'error',
        ];

        if (!$this->validate()) {
            $error = $this->getFirstErrors();
            $output['messages'] = $error['link'];
            return $output;
        }

        $lot = Lot::find()
            ->my()
            ->byLink($this->link)
            ->limit(1)
            ->one();

        if (!$lot) {
            $output['messages'] = 'Lot not found';
            return $output;
        }

        $offer = Offer::find()
            ->byLot($lot->id)
            ->statusLotNotDelete()
            ->limit(1)
            ->one();

        if ($offer) {
            $output['messages'] = 'Объявление нельзя удалить';
            return $output;
        }

        Lot::getDb()->transaction(function($db) use ($lot) {
            $lot->setStatusDelete();
            $lot->save();
        });

        if ($lot->hasErrors()) {
            /**
             * TODO: вывести ошибку понятную для пользователя вместо ошибки системы
             */
            $output['messages'] = 'При удалении возникла ошибка, попробуйте позже';
            return $output;
        }

        /**
         * TODO: удалить запросы на "твердо" или нет
         */

        $output['result'] = 'success';

        return $output;
    }

}
