<?php

declare(strict_types=1);

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * cd /home/w/webreadf/metressa.com; /usr/local/bin/php7.2 yii app/index
 */
class AppController extends Controller
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

}
