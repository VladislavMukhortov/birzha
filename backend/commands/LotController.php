<?php

declare(strict_types=1);

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

use app\models\Lot;

/**
 * cd /home/w/webreadf/metressa.com; /usr/local/bin/php7.2 yii lot/index
 */
class LotController extends Controller
{

    /**
     * Консольные команды контроллера
     * @return int
     */
    public function actionIndex() : int
    {
        echo 'Доступны следующие команды:' . PHP_EOL;
        echo 'app/php-version   : версия PHP' . PHP_EOL;
        return ExitCode::OK;
    }



    /**
     * Версия PHP
     * @return int Exit code
     */
    public function actionPhpVersion() : int
    {
        echo PHP_VERSION . PHP_EOL;
        return ExitCode::OK;
    }



    /**
     * Сброс значений объявлений которые торговались между двумя пользователями
     * и были завершены по истечению времени
     * Запускается каждую минуту
     * @return int Exit code
     */
    public function actionCheckEndedOffer() : int
    {
        $now = (int) time();

        $count = Lot::updateAll(
            [
                'offer_user_id' => NULL,
                'offer_price_1' => NULL,
                'offer_price_2' => NULL,
                'offer_price_3' => NULL,
                'demand_price_1' => NULL,
                'demand_price_2' => NULL,
                'offer_ended_at' => NULL,
            ], [
                '>', 'offer_ended_at', $now
            ]
        );

        echo "Updated lot: {$count}" . PHP_EOL;
        return ExitCode::OK;
    }


}
