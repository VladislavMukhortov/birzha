<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%company}}`.
 * Таблица компаний (контрагенты) и вся информация связанная с ними
 */
class m190516_101123_create_company_table extends Migration
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

        $this->createTable('{{%company}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(200)->notNull(),                    // название компании
            'location' => $this->string(200)->defaultValue(null),       // адресс компании
            'swift' => $this->string(200)->notNull(),                   // Society for Worldwide Interbank Financial Telecommunications
            'iban' => $this->string(60)->notNull(),                     // International Bank Account Number

            'bank_name' => $this->string(200)->defaultValue(null),      // название банка
            'bank_location' => $this->string(200)->defaultValue(null),  // адресс банка

            'email' => $this->string(200)->defaultValue(null),          // email
            'phone' => $this->string(200)->defaultValue(null),          // телефон
            'site' => $this->string(200)->defaultValue(null),           // сайт
            'director' => $this->string(200)->defaultValue(null),       // директор ФИО

            'text' => $this->string(500)->defaultValue(null),           // дополнительная информация (не обязательный параметр)
            'is_edit' => $this->boolean()->defaultValue(true),          // измененный
            'is_verify' => $this->boolean()->defaultValue(false),       // проверенный контрагент
            'status' => $this->boolean()->defaultValue(true),           // статус компании
            'verify_email' => $this->string(32)->defaultValue(null),    // код для подтверждения email (NULL - подтвержден)
            'verify_phone' => $this->string(4)->defaultValue(null),     // код для подтверждения номера телефона (NULL - подтвержден)
            'created_at' => 'timestamp DEFAULT NOW()',                  // дата создания
            'updated_at' => 'timestamp ON UPDATE NOW()'                 // дата изменения
        ], $tableOptions);



        // add data
        $this->batchInsert('{{%company}}',
            ['id', 'name',          'location', 'swift',    'iban',     'bank_name', 'bank_location'], [
            [1,    'Company #1',    'City #1',  '1111111',  '1111111',  'Bank #1',   'Bank city #1'],
            [2,    'Company #2',    'City #2',  '2222222',  '2222222',  'Bank #2',   'Bank city #2'],
            [3,    'Company #3',    'City #3',  '3333333',  '3333333',  'Bank #3',   'Bank city #3'],
            [4,    'Company #4',    'City #4',  '4444444',  '4444444',  'Bank #4',   'Bank city #4'],
            [5,    'Company #5',    'City #5',  '5555555',  '5555555',  'Bank #5',   'Bank city #5']
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%company}}');
    }
}
