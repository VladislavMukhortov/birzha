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
    }







    /**
     * отправляем пользователю сообщение для подтверждения привязки email к его странице
     * @param  User  $user пользователь
  */
    // public static function sendMailAboutChangeEmail(User $user)
    // {
    //     if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
    //         Yii::$app->mailer->compose([
    //             'html' => '_profile/settings/change-email-html',
    //             'text' => '_profile/settings/change-email-text'
    //         ], [
    //             'user' => $user
    //         ])
    //         ->setFrom([Yii::$app->params['noreplyEmail'] => Yii::$app->name . ' robot'])
    //         ->setTo($user->email)
    //         ->setSubject(Yii::$app->name . ' - Привязка email к странице')
    //         ->send();
    //     }
    // }


    /**
     * отправляем пользователю уведомление о новой рассылке на сайте
     * вызываем по крону в ProfileController->actionSendNoticeRequest
     * @param  array $user пользователь которому отправляем уведомление
     */
    // public static function sendMailNoticeRequest($user)
    // {
    //     if (filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
    //         $mailer = Yii::createObject([
    //             'class' => 'yii\swiftmailer\Mailer',
    //             'transport' => [
    //                 'class' => 'Swift_SmtpTransport',
    //                 'host' => 'smtp.beget.com',
    //                 'username' => 'notice-mailer@metressa.com',
    //                 'password' => '%ntCOqK8O*2ZaDUz',
    //                 'port' => '465',
    //                 'encryption' => 'ssl'
    //             ],
    //         ]);

    //         $mailer->compose([
    //             'html' => '_cron/notice-request-html',
    //             'text' => '_cron/notice-request-text'
    //         ], [
    //             'user' => $user
    //         ])
    //         ->setFrom([Yii::$app->params['noreplyEmail'] => Yii::$app->name . ' robot'])
    //         ->setTo($user['email'])
    //         ->setSubject(Yii::$app->name . ' - Свежее предложение')
    //         ->send();
    //     }
    // }





    /**
     * отправляем сообщение с формы обратной связи
     * вызываем в FeedbackForm->send
     * @param  array $data  данные с формы
     * @return boolean
     */
    // public static function sendFeedbackForm($data = [])
    // {
    //     $success = false;

    //     if (filter_var(Yii::$app->params['supportEmail'], FILTER_VALIDATE_EMAIL)) {
    //         $success = Yii::$app->mailer->compose([
    //             'text' => '_site/feedback-text'
    //         ], [
    //             'data' => $data
    //         ])
    //         ->setFrom([Yii::$app->params['noreplyEmail'] => $data['name']])
    //         ->setTo(Yii::$app->params['supportEmail'])
    //         ->setSubject(Yii::$app->name . ' - ' . $data['cause'])
    //         ->send();
    //     }

    //     return (boolean) $success;
    // }









}
