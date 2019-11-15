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

        $company = [];

        for ($i = 1; $i <= 1000; $i++) {
            $id = $i;
            $name = "Company #{$i}";
            $location = "City #{$i}";
            $swift = "111{$i}{$i}111";
            $iban = "222{$i}{$i}222";
            $bank_name = "Bank #{$i}";
            $bank_location = "Bank city #{$i}";

            $company[] = [
                $id,
                $name,
                $location,
                $swift,
                $iban,
                $bank_name,
                $bank_location
            ];
        }

        // add data
        $this->batchInsert('{{%company}}', [
            'id',
            'name',
            'location',
            'swift',
            'iban',
            'bank_name',
            'bank_location'
        ], $company);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%company}}');
    }
}
