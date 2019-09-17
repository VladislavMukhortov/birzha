<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lots}}`.
 * Таблица для объявлений
 */
class m190516_101136_create_lots_table extends Migration
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

        // https://graintrade.com.ua/ru/birzha/createbuy
        // http://zerno.market/aitems/kukuruzu
        // https://xn--c1acdaj1bho.xn--p1ai/
        // http://www.4sg.com.ua/board.php?b=10
        // http://corn.kz/
        // https://doska.zol.ru/
        // https://www.furazh.ru/declar/
        // https://www.zernotrader.ru/pshenica
        // https://agro.club/
        $this->createTable('{{%lots}}', [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned()->notNull(),                   // ID пользователя подавшего объявление
            'company_id' => $this->integer()->unsigned()->notNull(),                // ID компании от которой подается объявление
            'crop_id' => $this->integer()->unsigned()->notNull(),                   // ID культуры к которой пренадлежит объявление
            'deal' => $this->char(10)->notNull(),                                   // тип объявления buy sell
            'price' => $this->float()->unsigned()->notNull(),                       // цена
            'currency' => $this->char(3)->notNull(),                                // код валюты
            'quantity' => $this->integer()->unsigned()->notNull(),                  // кол-во тонн (объем)
            'period' => $this->string(200)->defaultValue(null),                     // период поставки

            'basis' => $this->char(3)->notNull(),                                   // базис поставки
            'fob_port' => $this->string(255)->defaultValue(null),                   // базис - порт
            'fob_terminal' => $this->string(255)->defaultValue(null),               // базис - терминал
            'cif_country' => $this->string(255)->defaultValue(null),                // базис - страна
            'cif_port' => $this->string(255)->defaultValue(null),                   // базис - порт

            'moisture' => $this->tinyInteger()->defaultValue(null),                 // влажность - 0-100%
            'foreign_matter' => $this->tinyInteger()->defaultValue(null),           // сорная примесь - 0-100%
            'grain_admixture' => $this->tinyInteger()->defaultValue(null),          // зерновая примесь - 0-100%
            'gluten' => $this->tinyInteger()->defaultValue(null),                   // клейковина - 12-40%
            'protein' => $this->tinyInteger()->defaultValue(null),                  // протеин - 0-80%
            'natural_weight' => $this->smallInteger()->defaultValue(null),          // натура - 50-1000 грам/литр
            'falling_number' => $this->smallInteger()->defaultValue(null),          // число падения - 50-500 штук
            'vitreousness' => $this->tinyInteger()->defaultValue(null),             // стекловидность - 20-95%
            'ragweed' => $this->smallInteger()->defaultValue(null),                 // амброзия - 0-500 штук/кг
            'bug' => $this->tinyInteger()->defaultValue(null),                      // клоп - 0-20%
            'oil_content' => $this->tinyInteger()->defaultValue(null),              // масличность - 0-80%
            'oil_admixture' => $this->tinyInteger()->defaultValue(null),            // масличная примесь - 0-100%
            'broken' => $this->tinyInteger()->defaultValue(null),                   // битые - 0-100%
            'damaged' => $this->tinyInteger()->defaultValue(null),                  // повреждённые - 0-100%
            'dirty' => $this->tinyInteger()->defaultValue(null),                    // маранные - 0-100%
            'ash' => $this->tinyInteger()->defaultValue(null),                      // зольность - 0-100%
            'erucidic_acid' => $this->tinyInteger()->defaultValue(null),            // эруковая кислота - 0-20%
            'peroxide_value' => $this->tinyInteger()->defaultValue(null),           // перекисное число - 0-20%
            'acid_value' => $this->tinyInteger()->defaultValue(null),               // кислотное число - 0-20%
            'other_color' => $this->tinyInteger()->defaultValue(null),              // другой цвет - 1-5%

            'crop_year' => $this->string(100)->defaultValue(null),                  // год урожая

            'text' => $this->string(500)->defaultValue(null),                       // дополнительная информация (не обязательный параметр)
            'link' => $this->string(200)->notNull()->unique(),                      // ссылка

            'offer_user_id' => $this->integer()->unsigned()->defaultValue(null),    // ID пользователя (второй стороны) для оффера
            'offer_price_1' => $this->float()->unsigned()->defaultValue(null),      // первая цена оффера от второй стороны
            'offer_price_2' => $this->float()->unsigned()->defaultValue(null),      // вторая цена оффера от второй стороны
            'offer_price_3' => $this->float()->unsigned()->defaultValue(null),      // третья цена оффера от второй стороны
            'demand_price_1' => $this->float()->unsigned()->defaultValue(null),     // ответ владельца на первую цену от второй стороны
            'demand_price_2' => $this->float()->unsigned()->defaultValue(null),     // ответ владельца на вторую цену от второй стороны
            'offer_ended_at' => $this->timestamp()->defaultValue(null),             // время окончания торга между пользователями

            // 'views' => $this->integer()->unsigned()->defaultValue(0),               // кол-во просмотров
            'is_edit' => $this->boolean()->defaultValue(true),                      // измененный
            'status' => $this->boolean()->defaultValue(true),                       // статус объявления
            'created_at' => 'timestamp DEFAULT NOW()',                              // дата создания
            'updated_at' => 'timestamp ON UPDATE NOW()'                             // дата изменения
        ], $tableOptions);

        $table_name = $this->db->getSchema()->getRawTableName('{{%lots}}');

        // creates index for column `deal`
        $this->createIndex(
            "{$table_name}_deal_idx",   // name
            "{$table_name}",            // table
            'deal',                     // column
            false                       // unique
        );

        // add foreign key for table `users`
        $this->addForeignKey(
            "{$table_name}_user_id_fk",
            "{$table_name}",
            'user_id',
            '{{%users}}',
            'id',
            'NO ACTION',
            'CASCADE'
        );
        // add foreign key for table `company`
        $this->addForeignKey(
            "{$table_name}_company_id_fk",
            "{$table_name}",
            'company_id',
            '{{%company}}',
            'id',
            'NO ACTION',
            'CASCADE'
        );
        // add foreign key for table `crops`
        $this->addForeignKey(
            "{$table_name}_crop_id_fk",
            "{$table_name}",
            'crop_id',
            '{{%crops}}',
            'id',
            'NO ACTION',
            'CASCADE'
        );

        $crops_list = [
            'wheat',        // пшеница
            'durum',        // пшеница твердая
            'barley',       // ячмень
            'corn',         // кукуруза
            'flax',         // лен
            'rape',         // рапс
            'peas',         // горох
            'soybeans',     // соевые бобы
            'sunflower',    // подсолнечник
            'chickpeas',    // нут
            'wild_flax',    // рыжик
            'safflower',    // сафлор
            'sorghum',      // сорго
            'millet',       // просо
            'coriander',    // кориандр
            'mustard',      // горчица
            'lentil',       // чечевица
            'rye',          // рожь
            'oat',          // овес
            'buckwheat',    // гречиха
            'triticale',    // тритикале
            'rice'          // рис
        ];

        $crops_count = count($crops_list);

        $currency_list = ['USD','EUR','RUB','UAH','KZT','CNY'];
        $currency_count = count($currency_list) - 1;

        $basis_list = ['FOB','CIF'];
        $basis_count = count($basis_list) - 1;

        $lots = [];
        for ($i = 1; $i <= 1000; $i++) {
            $id = $i;
            $user_id = $i;
            $company_id = 1;
            if ($i >= 200) {
                $company_id = 2;
            }
            if ($i >= 400) {
                $company_id = 3;
            }
            if ($i >= 600) {
                $company_id = 4;
            }
            if ($i >= 800) {
                $company_id = 5;
            }
            $crop_id = rand(1, $crops_count);
            $deal = ((boolean) rand(0, 1)) ? 'buy' : 'sell';
            $price = rand(1000, 9999);
            $currency = $currency_list[rand(0, $currency_count)];
            $quantity = rand(100, 9999);
            $period = 'June - AUG';
            $basis = $basis_list[rand(0, $basis_count)];
            $fob_port = NULL;
            $fob_terminal = NULL;
            $cif_country = NULL;
            $cif_port = NULL;
            if ($basis == 'FOB') {
                $fob_port = 'Ильичевский МТП';
                $fob_terminal = 'ИЛЬИЧЕВСК ТРАНС СЕРВИС, ООО';
            } else if ($basis == 'CIF') {
                $cif_country = 'Россия';
                $cif_port = 'Ильичевский МТП';
            }
            $moisture = rand(0, 100);       // влажность - 0-100%
            $foreign_matter = rand(0, 100); // сорная примесь - 0-100%
            $grain_admixture = NULL;        // зерновая примесь - 0-100%
            $gluten = NULL;                 // клейковина - 12-40%
            $protein = NULL;                // протеин - 0-80%
            $natural_weight = NULL;         // натура - 50-1000 грам/литр
            $falling_number = NULL;         // число падения - 50-500 штук
            $vitreousness = NULL;           // стекловидность - 20-95%
            $ragweed = NULL;                // амброзия - 0-500 штук/кг
            $bug = NULL;                    // клоп - 0-20%
            $oil_content = NULL;            // масличность - 0-80%
            $oil_admixture = NULL;          // масличная примесь - 0-100%
            $broken = NULL;                 // битые - 0-100%
            $damaged = NULL;                // повреждённые - 0-100%
            $dirty = NULL;                  // маранные - 0-100%
            $ash = NULL;                    // зольность - 0-100%
            $erucidic_acid = NULL;          // эруковая кислота - 0-20%
            $peroxide_value = NULL;         // перекисное число - 0-20%
            $acid_value = NULL;             // кислотное число - 0-20%
            // пшеница
            if ($crop_id === 1) {
                $grain_admixture = rand(0, 100);// зерновая примесь - 0-100%
                $gluten = rand(12, 40);         // клейковина - 12-40%
                $protein = rand(0, 80);        // протеин - 0-80%
                $natural_weight = rand(50, 1000); // натура - 50-1000 грам/литр
                $falling_number = rand(50, 500); // число падения - 50-500 штук
                $bug = rand(0, 20);            // клоп - 0-20%
            }
            // пшеница твердая
            if ($crop_id === 2) {
                $grain_admixture = rand(0, 100);// зерновая примесь - 0-100%
                $gluten = rand(12, 40); // клейковина - 12-40%
                $protein = rand(0, 80);    // протеин - 0-80%
                $natural_weight = rand(50, 1000); // натура - 50-1000 грам/литр
                $vitreousness = rand(20, 95);   // стекловидность - 20-95%
                $bug = rand(0, 20);    // клоп - 0-20%
            }
            // ячмень
            if ($crop_id === 3) {
                $grain_admixture = rand(0, 100);// зерновая примесь - 0-100%
                $natural_weight = rand(50, 1000); // натура - 50-1000 грам/литр
            }
            // кукуруза
            if ($crop_id === 4) {
                $ragweed = rand(0, 500);    // амброзия - 0-500 штук/кг
                $broken = rand(0, 100); // битые - 0-100%
                $damaged = rand(0, 100);    // повреждённые - 0-100%
            }
            // лен
            if ($crop_id === 5) {
                $oil_content = rand(0, 80);    // масличность - 0-80%
                $peroxide_value = rand(0, 20); // перекисное число - 0-20%
                $acid_value = rand(0, 20);     // кислотное число - 0-20%
            }
            // рапс
            if ($crop_id === 6) {
                $oil_content = rand(0, 80);    // масличность - 0-80%
                $oil_admixture = rand(0, 100);  // масличная примесь - 0-100%
                $erucidic_acid = rand(0, 20);  // эруковая кислота - 0-20%
                $peroxide_value = rand(0, 20); // перекисное число - 0-20%
                $acid_value = rand(0, 20);     // кислотное число - 0-20%
            }
            // горох
            if ($crop_id === 7) {
                $broken = rand(0, 100); // битые - 0-100%
                $damaged = rand(0, 100);    // повреждённые - 0-100%
            }
            // соевые бобы
            if ($crop_id === 8) {
                $protein = rand(0, 80); // протеин - 0-80%
                $oil_content = rand(0, 80); // масличность - 0-80%
            }
            // подсолнечник
            if ($crop_id === 9) {
                $oil_content = rand(0, 80);    // масличность - 0-80%
                $oil_admixture = rand(0, 100);  // масличная примесь - 0-100%
                $peroxide_value = rand(0, 20); // перекисное число - 0-20%
                $acid_value = rand(0, 20);     // кислотное число - 0-20%
            }
            // нут
            if ($crop_id === 10) {
                $broken = rand(0, 100);                 // битые - 0-100%
                $dirty = rand(0, 100);                  // маранные - 0-100%
            }
            // рыжик
            if ($crop_id === 11) {
                $oil_content = rand(0, 80);    // масличность - 0-80%
                $oil_admixture = rand(0, 100);  // масличная примесь - 0-100%
            }
            // сафлор
            if ($crop_id === 12) {
                $oil_content = rand(0, 80);    // масличность - 0-80%
            }
            // кориандр
            if ($crop_id === 15) {
                $broken = rand(0, 100);                 // битые - 0-100%
            }
            // горчица
            if ($crop_id === 16) {
                $oil_content = rand(0, 80);    // масличность - 0-80%
            }
            // чечевица
            if ($crop_id === 17) {
                $broken = rand(0, 100);                 // битые - 0-100%
            }
            // рожь
            if ($crop_id === 18) {
                $grain_admixture = rand(0, 100);    // зерновая примесь - 0-100%
                $natural_weight = rand(50, 1000);   // натура - 50-1000 грам/литр
                $falling_number = rand(50, 500);    // число падения - 50-500 штук
            }
            // овес
            if ($crop_id === 19) {
                $grain_admixture = rand(0, 100);        // зерновая примесь - 0-100%
                $natural_weight = rand(50, 1000);         // натура - 50-1000 грам/литр
            }
            // гречиха
            if ($crop_id === 20) {
                $grain_admixture = rand(0, 100);        // зерновая примесь - 0-100%
            }
            // тритикале
            if ($crop_id === 21) {
                $protein = rand(0, 80);    // протеин - 0-80%
                $natural_weight = rand(50, 1000);    // натура - 50-1000 грам/литр
                $ash = rand(0, 100);    // зольность - 0-100%
            }
            // рис
            if ($crop_id === 22) {
                $broken = rand(0, 100); // битые - 0-100%
            }
            $link = security()->generateRandomString(15);

            $lots[] = [
                $id,
                $user_id,
                $company_id,
                $crop_id,
                $deal,
                $price,
                $currency,
                $quantity,
                $period,
                $basis,
                $fob_port,
                $fob_terminal,
                $cif_country,
                $cif_port,
                $moisture,
                $foreign_matter,
                $grain_admixture,
                $gluten,
                $protein,
                $natural_weight,
                $falling_number,
                $vitreousness,
                $ragweed,
                $bug,
                $oil_content,
                $oil_admixture,
                $broken,
                $damaged,
                $dirty,
                $ash,
                $erucidic_acid,
                $peroxide_value,
                $acid_value,
                $link
            ];
        }

        // add data
        $this->batchInsert('{{%lots}}', [
            'id',
            'user_id',      // ID пользователя подавшего объявление
            'company_id',   // ID компании от которой подается объявление
            'crop_id',      // ID культуры к которой пренадлежит объявление
            'deal',         // тип объявления buy sell
            'price',        // цена
            'currency',     // код валюты
            'quantity',     // кол-во тонн (объем)
            'period',       // период поставки
            'basis',        // базис поставки
            'fob_port',     // базис - порт
            'fob_terminal', // базис - терминал
            'cif_country',  // базис - страна
            'cif_port',     // базис - порт
            'moisture',
            'foreign_matter',
            'grain_admixture',
            'gluten',
            'protein',
            'natural_weight',
            'falling_number',
            'vitreousness',
            'ragweed',
            'bug',
            'oil_content',
            'oil_admixture',
            'broken',
            'damaged',
            'dirty',
            'ash',
            'erucidic_acid',
            'peroxide_value',
            'acid_value',
            'link'          // ссылка
        ], $lots);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%lots}}');
    }
}
