<?php
declare(strict_types=1);

namespace app\models\notice;

use Yii;

use app\models\User;
use app\models\Company;

/**
 * Email уведомления
 */
class EmailNotification
{


    /**
     * Регистрация пользователя - отправляем ему уведомление, пароль и ссылку на подверждение
     * @param  User     $user       пользователь
     * @param  string   $password   сгенерированный пароль пользователя
     * @return boolean              было ли сообщение успешно отправлено
     */
    public static function newUserRegistration(User $user, string $password) : bool
    {
        if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            $mailer = Yii::createObject([
                'class' => 'yii\swiftmailer\Mailer',
                'transport' => [
                    'class' => 'Swift_SmtpTransport',
                    'host' => getenv('MAILER_HOST'),
                    'username' => getenv('MAILER_REGISTER_USERNAME'),
                    'password' => getenv('MAILER_REGISTER_PASSWORD'),
                    'port' => getenv('MAILER_PORT'),
                    'encryption' => getenv('MAILER_ENCRYPTION')
                ]
            ]);

            $sending = $mailer->compose([
                'html' => 'user/new-registration-html',
                'text' => 'user/new-registration-text'
            ], [
                'user' => $user,
                'password' => $password
            ])
            ->setFrom([getenv('MAILER_REGISTER_USERNAME') => Yii::$app->name . ' robot'])
            ->setTo($user->email)
            ->setSubject(Yii::$app->name . ' - Подтверждение регистрации')
            ->send();

            return (boolean) $sending;
        }
		return false;
    }



    /**
     * Изменение пароля у пользователя
     * @param  User     $user       пользователь
     * @param  string   $password   новый пароль
     * @return boolean              было ли сообщение успешно отправлено
     */
    public static function userChangePassword(User $user, string $password) : bool
    {
        if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            $mailer = Yii::createObject([
                'class' => 'yii\swiftmailer\Mailer',
                'transport' => [
                    'class' => 'Swift_SmtpTransport',
                    'host' => getenv('MAILER_HOST'),
                    'username' => getenv('MAILER_PRIMARY_USERNAME'),
                    'password' => getenv('MAILER_PRIMARY_PASSWORD'),
                    'port' => getenv('MAILER_PORT'),
                    'encryption' => getenv('MAILER_ENCRYPTION')
                ]
            ]);

            $sending = $mailer->compose([
                'html' => 'user/change-password-html',
                'text' => 'user/change-password-text'
            ], [
                'user' => $user,
                'password' => $password
            ])
            ->setFrom([getenv('MAILER_PRIMARY_USERNAME') => Yii::$app->name . ' robot'])
            ->setTo($user->email)
            ->setSubject(Yii::$app->name . ' - Изменение пароля')
            ->send();

            return (boolean) $sending;
        }
		return false;
    }



    /**
     * Запрос на восстановление пароля
     * @param  User     $user   пользователь
     * @return boolean          было ли сообщение успешно отправлено
     */
    public static function requestPasswordReset(User $user) : bool
    {
        if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            $mailer = Yii::createObject([
                'class' => 'yii\swiftmailer\Mailer',
                'transport' => [
                    'class' => 'Swift_SmtpTransport',
                    'host' => getenv('MAILER_HOST'),
                    'username' => getenv('MAILER_PRIMARY_USERNAME'),
                    'password' => getenv('MAILER_PRIMARY_PASSWORD'),
                    'port' => getenv('MAILER_PORT'),
                    'encryption' => getenv('MAILER_ENCRYPTION')
                ]
            ]);

            $sending = $mailer->compose([
                'html' => 'user/request-password-reset-html',
                'text' => 'user/request-password-reset-text'
            ], [
                'user' => $user
            ])
            ->setFrom([getenv('MAILER_PRIMARY_USERNAME') => Yii::$app->name . ' robot'])
            ->setTo($user->email)
            ->setSubject(Yii::$app->name . ' - Восстановление пароля')
            ->send();

            return (boolean) $sending;
        }
		return false;
    }



    /**
     * Изменение почтового ящика у пользователя
     * @param  User     $user       пользователь
     * @return boolean              было ли сообщение успешно отправлено
     */
    public static function userChangeEmail(User $user) : bool
    {
        if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            $mailer = Yii::createObject([
                'class' => 'yii\swiftmailer\Mailer',
                'transport' => [
                    'class' => 'Swift_SmtpTransport',
                    'host' => getenv('MAILER_HOST'),
                    'username' => getenv('MAILER_PRIMARY_USERNAME'),
                    'password' => getenv('MAILER_PRIMARY_PASSWORD'),
                    'port' => getenv('MAILER_PORT'),
                    'encryption' => getenv('MAILER_ENCRYPTION')
                ]
            ]);

            $sending = $mailer->compose([
                'html' => 'user/change-email-html',
                'text' => 'user/change-email-text'
            ], [
                'user' => $user
            ])
            ->setFrom([getenv('MAILER_PRIMARY_USERNAME') => Yii::$app->name . ' robot'])
            ->setTo($user->email)
            ->setSubject(Yii::$app->name . ' - Изменение почтового ящика')
            ->send();

            return (boolean) $sending;
        }
		return false;
    }



    /**
     * Изменение почтового ящика у контрагента
     * @param  Company  $company    контрагент
     * @return boolean              было ли сообщение успешно отправлено
     */
    public static function companyChangeEmail(Company $company) : bool
    {
        if (filter_var($company->email, FILTER_VALIDATE_EMAIL)) {
            $mailer = Yii::createObject([
                'class' => 'yii\swiftmailer\Mailer',
                'transport' => [
                    'class' => 'Swift_SmtpTransport',
                    'host' => getenv('MAILER_HOST'),
                    'username' => getenv('MAILER_PRIMARY_USERNAME'),
                    'password' => getenv('MAILER_PRIMARY_PASSWORD'),
                    'port' => getenv('MAILER_PORT'),
                    'encryption' => getenv('MAILER_ENCRYPTION')
                ]
            ]);

            $sending = $mailer->compose([
                'html' => 'company/change-email-html',
                'text' => 'company/change-email-text'
            ], [
                'company' => $company
            ])
            ->setFrom([getenv('MAILER_PRIMARY_USERNAME') => Yii::$app->name . ' robot'])
            ->setTo($company->email)
            ->setSubject(Yii::$app->name . ' - Изменение почтового ящика')
            ->send();

            return (boolean) $sending;
        }
		return false;
    }

}
