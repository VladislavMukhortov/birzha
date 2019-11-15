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

if (!function_exists('convertTimeZoneFromJS')) {
    /**
     * Преобразуем часовой пояс полученный от пользователя в строку для хранения в БД
     *
     * @param  string $tz
     * @return string
     */
    function convertTimeZoneFromJS($tz = '') : string
    {
        switch ($tz) {
            case '0'    : return 'UTC';                  // +00:00
            case '-60'  : return 'Europe/Berlin';        // +01:00
            case '60'   : return 'America/Scoresbysund'; // -01:00
            case '-120' : return 'Europe/Kiev';          // +02:00
            case '120'  : return 'America/Noronha';      // -02:00
            case '-180' : return 'Europe/Moscow';        // +03:00
            case '180'  : return 'America/Santiago';     // -03:00
            case '-210' : return 'Asia/Tehran';          // +03:30
            case '210'  : return 'America/St_Johns';     // -03:30
            case '-240' : return 'Asia/Dubai';           // +04:00
            case '240'  : return 'America/Dominica';     // -04:00
            case '-270' : return 'Asia/Kabul';           // +04:30
            case '-300' : return 'Asia/Yekaterinburg';   // +05:00
            case '300'  : return 'America/Cayman';       // -05:00
            case '-330' : return 'Asia/Colombo';         // +05:30
            case '-345' : return 'Asia/Kathmandu';       // +05:45
            case '-360' : return 'Asia/Omsk';            // +06:00
            case '360'  : return 'America/Chicago';      // -06:00
            case '-390' : return 'Asia/Yangon';          // +06:30
            case '-420' : return 'Asia/Bangkok';         // +07:00
            case '420'  : return 'America/Chihuahua';    // -07:00
            case '-480' : return 'Asia/Hong_Kong';       // +08:00
            case '480'  : return 'America/Los_Angeles';  // -08:00
            case '-525' : return 'Australia/Eucla';      // +08:45
            case '-540' : return 'Asia/Tokyo';           // +09:00
            case '540'  : return 'America/Anchorage';    // -09:00
            case '-570' : return 'Australia/Darwin';     // +09:30
            case '570'  : return 'Pacific/Marquesas';    // -09:30
            case '-600' : return 'Asia/Vladivostok';     // +10:00
            case '600'  : return 'America/Adak';         // -10:00
            case '-630' : return 'Australia/Adelaide';   // +10:30
            case '-660' : return 'Australia/Sydney';     // +11:00
            case '660'  : return 'Pacific/Midway';       // -11:00
            case '-720' : return 'Asia/Kamchatka';       // +12:00
            case '-780' : return 'Antarctica/McMurdo';   // +13:00
            case '-840' : return 'Pacific/Apia';         // +14:00
            default     : return 'UTC';                  // +00:00
        }
    }
}

