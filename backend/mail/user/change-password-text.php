<?php

/**
 * отправка сообщения при изменении пароля
 * $user      User   пользователь
 * $password  string пароль пользователя
 */

// ссылка для восстановления пароля
$password_reset_link = Yii::$app->params['frontendURL'] . 'auth/password-reset';

?>

Здравствуйте, <?= h($user->name) ?>!

Вы только что изменили пароль. Если это были не вы то восстановите пароль
<?= h($password_reset_link) ?>


Ваши учетные данные:
Email: <?= h($user->email) ?>
Phone: <?= h($user->phone) ?>
Пароль: <?= h($password) ?>
