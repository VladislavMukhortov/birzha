<?php

declare(strict_types=1);

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * cd /home/w/webreadf/metressa.com; /usr/local/bin/php7.2 yii backup/index
 */
class BackupController extends Controller
{

    /**
     * Консольные команды контроллера
     *
     * @return int
     */
    public function actionIndex() : int
    {
        echo 'Доступны следующие команды:' . PHP_EOL;
        echo 'backup/save-files : резервная копия файлов' . PHP_EOL;
        echo 'backup/save-db    : резервная копия базы данных' . PHP_EOL;
        return ExitCode::OK;
    }



    /**
     * Резервная копия файлов
     *
     * Запускается в минимальный пик нагрузки (предположительно ночью - утром)
     * @return int Exit code
     */
    public function actionSaveFiles() : int
    {
        return ExitCode::OK;
    }



    /**
     * Резервная копия базы данных
     *
     * Запускается в минимальный пик нагрузки (предположительно ночью - утром)
     * @return int Exit code
     */
    public function actionSaveDb() : int
    {
        return ExitCode::OK;
    }

}
