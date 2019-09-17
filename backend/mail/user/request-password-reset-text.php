<?php

/**
 * отправка сообщения при сбросе (восстановлении) пароля
 * $user    User    пользователь
 */

// ссылка для сброса пароля
$password_reset_link = Yii::$app->params['frontendURL'] . 'auth/password-reset/' . $user->password_reset_token;

?>

Здравствуйте, <?= h($user->name) ?>!

Перейдите по ссылке, чтобы сбросить пароль
<?= h($password_reset_link) ?>
