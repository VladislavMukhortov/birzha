<?php

declare(strict_types=1);

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * cd /home/w/webreadf/metressa.com; /usr/local/bin/php7.2 yii rbac/index
 */
class RbacController extends Controller
{

    /**
     * Консольные команды контроллера
     * @return int
     */
    public function actionIndex() : int
    {
        echo 'Доступны следующие команды:' . PHP_EOL;
        echo 'rbac/init   : добавление ролей доступа' . PHP_EOL;
        return ExitCode::OK;
    }



    /**
     * Добавление ролей доступа
     * @return int
     */
    public function actionInit() : int
    {
        $auth = app()->authManager;

        // добавляем роль "admin"
        $admin = $auth->createRole('admin');
        $auth->add($admin);

        $auth->assign($admin, 1);

        return ExitCode::OK;
    }

}
