<?php

/**
 * отправка сообщения при изменении пароля
 * $user      User   пользователь
 * $password  string пароль пользователя
 */

// ссылка для восстановления пароля
$password_reset_link = Yii::$app->params['frontendURL'] . 'auth/password-reset';

?>

<div>Здравствуйте, <?= h($user->name) ?>!</div>
<div>Вы только что изменили пароль. Если это были не вы то восстановите пароль</div>
<div><a href="<?= h($password_reset_link) ?>"><?= h($password_reset_link) ?></a></div>

<div>Ваши учетные данные:</div>
<div>Email: <?= h($user->email) ?></div>
<div>Phone: <?= h($user->phone) ?></div>
<div>Пароль: <?= h($password) ?></div>
