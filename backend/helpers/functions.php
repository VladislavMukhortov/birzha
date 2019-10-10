<?php

declare(strict_types=1);

// Core components
if (!function_exists('app')) {
    /**
     * @return ConsoleApplication|WebApplication
     */
    function app()
    {
        return Yii::$app;
    }
}

if (!function_exists('db')) {
    /**
     * @return yii\db\Connection
     */
    function db() : yii\db\Connection
    {
        return Yii::$app->getDb();
    }
}

if (!function_exists('formatter')) {
    /**
     * @return app\components\Formatter
     */
    function formatter()
    {
        return Yii::$app->getFormatter();
    }
}

if (!function_exists('request')) {
    /**
     * @return yii\console\Request|yii\web\Request
     */
    function request()
    {
        return Yii::$app->GetRequest();
    }
}

if (!function_exists('response')) {
    /**
     * @return yii\console\Response|yii\web\Response
     */
    function response()
    {
        return Yii::$app->getResponse();
    }
}

if (!function_exists('session')) {
    /**
     * @return yii\web\Session
     */
    function session() : yii\web\Session
    {
        return Yii::$app->getSession();
    }
}

if (!function_exists('security')) {
    /**
     * @return yii\base\Security
     */
    function security() : yii\base\Security
    {
        return Yii::$app->getSecurity();
    }
}

if (!function_exists('user')) {
    /**
     * @return yii\web\User
     */
    function user() : yii\web\User
    {
        return Yii::$app->getUser();
    }
}

// Other
if (!function_exists('url')) {
    /**
     * @param string $url
     * @param bool $scheme
     * @return string
     */
    function url($url = '', $scheme = false) : string
    {
        return yii\helpers\Url::to($url, $scheme);
    }
}

if (!function_exists('h')) {
    /**
     * @param $string
     * @return string
     */
    function h(string $string) : string
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('h')) {
    /**
     * @param $string
     * @return string
     */
    function h(string $string) : string
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('tofloat')) {
    /**
     * Форматирует цену введеное пользователем в корректное число типа float
     * @param $string
     * @return string
     */
    function tofloat($n) : float
    {
        $n = (string) $n;
        $dotPos = strrpos($n, '.');
        $commaPos = strrpos($n, ',');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
            ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

        $p = '/[^0-9]/';
        if (!$sep) {
            return floatval(preg_replace($p, '', $n));
        }

        return floatval(preg_replace($p, '', substr($n, 0, $sep)) . '.' . preg_replace($p, '', substr($n, $sep + 1, strlen($n))));
    }
}

