<?php

declare(strict_types=1);

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

use app\models\Offer;

/**
 * cd /home/w/webreadf/metressa.com; /usr/local/bin/php7.2 yii offer/index
 */
class OfferController extends Controller
{

    /**
     * Консольные команды контроллера
     * @return int
     */
    public function actionIndex() : int
    {
        echo 'Доступны следующие команды:' . PHP_EOL;
        echo 'offer/check-status-offers   : удаление просроченных офферов в статусе \'ТВЕРДО\'' . PHP_EOL;
        return ExitCode::OK;
    }



    /**
     * Проверка просроченных оферов в статусе "ТВЕРДО"
     *
     * Статус "твердо" у оффера может быть отклонен по истечению 3 попыток торга
     * или по истечению времени, которое дает владелец объявления.
     * Тут мы меняем статус с "твердо" на неактивный по истечению времени статуса "твердо"
     *
     * Запускается каждую минуту
     * @return int Exit code
     */
    public function actionCheckStatusOffers() : int
    {
        $now = (int) time();

        $count_status_auction = Offer::updateAll(
            [
                'status' => Offer::STATUS_INACTIVE,
                'ended_at' => NULL,
            ], [
                'and'
                ['status' => Offer::STATUS_AUCTION],
                ['>', 'ended_at', $now]
            ]
        );

        echo "Removal of expired offers: {$count_status_auction}" . PHP_EOL;

        return ExitCode::OK;
    }


}
