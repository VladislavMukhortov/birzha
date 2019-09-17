<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%data_change}}`.
 * Таблица для регистрации изменений данных произведенных пользователем
 * Записываем в таблицу только старый параметр который будет заменен новым
 * Даные меняются в таблицах "COMPANY", "USERS", "LOTS" ... и другие в которых пользователь может изменять данные
 */
class m190801_070150_create_data_change_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%data_change}}', [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned()->notNull(),   // ID пользователя сделавший изменение
            'key' => $this->string(255)->defaultValue(null),        // название параметра который меняем
            'val' => $this->string(500)->defaultValue(null),        // значение старого параметра
            'created_at' => 'timestamp DEFAULT NOW()',              // дата создания
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%data_change}}');
    }
}
