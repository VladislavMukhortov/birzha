<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row>

                    <b-col cols="12" md="8">

                        <h1 class="section_title">Новая заявка</h1>

                        <!-- STEP NUMBER -->
                        <div class="orders-steps">
                            <div class="orders-steps-item" v-bind:class="{ '_active': step_1 }">
                                <strong class="orders-steps-item-link" v-on:click="oneStep">Товар объём цена</strong>
                                <div>Подсказка</div>
                            </div>
                            <div class="orders-steps-item" v-bind:class="{ '_active': step_2 }">
                                <strong class="orders-steps-item-link" v-on:click="twoStep">Качество товара</strong>
                                <div>Подсказка</div>
                            </div>
                            <div class="orders-steps-item" v-bind:class="{ '_active': step_3 }">
                                <strong class="orders-steps-item-link" v-on:click="threeStep">Место поставки</strong>
                                <div>Подсказка</div>
                            </div>
                        </div>


                        <!-- STEP 1 -->
                        <div class="orders-step" v-bind:class="{ '_active': step_1 }">
                            <b-form-group>
                                <b-radio-group
                                    v-model="deal"
                                    v-bind:options="dealList"
                                    buttons
                                    button-variant="outline-primary"
                                    name="deal-radios"></b-radio-group>
                            </b-form-group>

                            <b-form-group
                                label="Выберите культуру:"
                                label-class="required"
                                label-for="create-crop-id">
                                <b-select
                                    required
                                    id="create-crop-id"
                                    v-model="cropId">
                                    <option v-bind:value="0" disabled>Наименование товара</option>
                                    <template v-for="crop_item in cropsList">
                                        <option v-bind:value="crop_item.id">{{ crop_item.name }}</option>
                                    </template>
                                </b-select>
                            </b-form-group>

                            <b-form-group
                                label="Валюта и цена за тонну:"
                                label-class="required"
                                label-for="create-price">
                                <b-input-group>

                                    <b-input-group-prepend>
                                        <b-select v-model="currency">
                                            <template v-for="currency_item in currencyList">
                                                <option v-bind:value="currency_item.name">{{ currency_item.name }}</option>
                                            </template>
                                        </b-select>
                                    </b-input-group-prepend>

                                    <b-input
                                        required
                                        id="create-price"
                                        v-model="price"
                                        type="text"></b-input>

                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                label="Укажите объём тонн:"
                                label-class="required"
                                label-for="create-quantity">
                                <b-input
                                    required
                                    id="create-quantity"
                                    v-model="quantity"
                                    type="text"></b-input>
                            </b-form-group>

                            <b-button
                                variant="primary"
                                v-bind:class="{ 'disabled': !firstStepCompleted }"
                                v-on:click="twoStep">Качество товара</b-button>
                        </div>


                        <!-- STEP 2 -->
                        <div class="orders-step" v-bind:class="{ '_active': step_2 }">

                            <b-form-group
                                label="Влажность:"
                                label-class="required"
                                label-for="create-moisture"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input
                                        required
                                        id="create-moisture"
                                        v-model="moisture"
                                        type="number"
                                        min="0"
                                        max="100"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                label="Сорная примесь:"
                                label-class="required"
                                label-for="create-foreign-matter"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input
                                        required
                                        id="create-foreign-matter"
                                        v-model="foreignMatter"
                                        type="number"
                                        min="0"
                                        max="100"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="grainAdmixtureVievs"
                                label="Зерновая примесь:"
                                label-class="required"
                                label-for="create-grain-admixture"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input
                                        required
                                        id="create-grain-admixture"
                                        v-model="grainAdmixture"
                                        type="number"
                                        min="0"
                                        max="100"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="glutenVievs"
                                label="Клейковина:"
                                label-class="required"
                                label-for="create-gluten"
                                label-cols="4"
                                description="Значение 12 - 40%">
                                <b-input-group append="%">
                                    <b-input
                                        required
                                        id="create-gluten"
                                        v-model="gluten"
                                        type="number"
                                        min="12"
                                        max="40"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="proteinVievs"
                                label="Протеин:"
                                label-class="required"
                                label-for="create-protein"
                                label-cols="4"
                                description="Значение 0 - 80%">
                                <b-input-group append="%">
                                    <b-input
                                        required
                                        id="create-protein"
                                        v-model="protein"
                                        type="number"
                                        min="0"
                                        max="80"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="naturalWeightVievs"
                                label="Натура:"
                                label-class="required"
                                label-for="create-natural-weight"
                                label-cols="4"
                                description="Значение 50 - 1000 грам/литр">
                                <b-input-group append="грам/литр">
                                    <b-input
                                        required
                                        id="create-natural-weight"
                                        v-model="naturalWeight"
                                        type="number"
                                        min="50"
                                        max="1000"></b-input>
                                    </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="fallingNumberVievs"
                                label="Число падения:"
                                label-class="required"
                                label-for="create-falling-number"
                                label-cols="4"
                                description="Значение 50 - 500 штук">
                                <b-input-group append="штук">
                                    <b-input
                                        required
                                        id="create-falling-number"
                                        v-model="fallingNumber"
                                        type="number"
                                        min="50"
                                        max="500"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="vitreousnessVievs"
                                label="Стекловидность:"
                                label-class="required"
                                label-for="create-vitreousness"
                                label-cols="4"
                                description="Значение 20 - 95%">
                                <b-input-group append="%">
                                    <b-input
                                        required
                                        id="create-vitreousness"
                                        v-model="vitreousness"
                                        type="number"
                                        min="20"
                                        max="95"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="ragweedVievs"
                                label="Амброзия:"
                                label-class="required"
                                label-for="create-ragweed"
                                label-cols="4"
                                description="Значение 0 - 500 штук/кг">
                                <b-input-group append="штук/кг">
                                    <b-input
                                        required
                                        id="create-ragweed"
                                        v-model="ragweed"
                                        type="number"
                                        min="0"
                                        max="500"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="bugVievs"
                                label="Клоп:"
                                label-class="required"
                                label-for="create-bug"
                                label-cols="4"
                                description="Значение 0 - 20%">
                                <b-input-group append="%">
                                    <b-input
                                        required
                                        id="create-bug"
                                        v-model="bug"
                                        type="number"
                                        min="0"
                                        max="20"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="oilContentVievs"
                                label="Масличность:"
                                label-class="required"
                                label-for="create-oil-content"
                                label-cols="4"
                                description="Значение 0 - 80%">
                                <b-input-group append="%">
                                    <b-input
                                        required
                                        id="create-oil-content"
                                        v-model="oilContent"
                                        type="number"
                                        min="0"
                                        max="80"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="oilAdmixtureVievs"
                                label="Масличная примесь:"
                                label-class="required"
                                label-for="create-oil-admixture"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input
                                        required
                                        id="create-oil-admixture"
                                        v-model="oilAdmixture"
                                        type="number"
                                        min="0"
                                        max="100"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="brokenVievs"
                                label="Битые:"
                                label-class="required"
                                label-for="create-broken"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input
                                        required
                                        id="create-broken"
                                        v-model="broken"
                                        type="number"
                                        min="0"
                                        max="100"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="damagedVievs"
                                label="Повреждённые:"
                                label-class="required"
                                label-for="create-damaged"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input
                                        required
                                        id="create-damaged"
                                        v-model="damaged"
                                        type="number"
                                        min="0"
                                        max="100"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="dirtyVievs"
                                label="Маранные:"
                                label-class="required"
                                label-for="create-dirty"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input
                                        required
                                        id="create-dirty"
                                        v-model="dirty"
                                        type="number"
                                        min="0"
                                        max="100"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="ashVievs"
                                label="Зольность:"
                                label-class="required"
                                label-for="create-ash"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input
                                        required
                                        id="create-ash"
                                        v-model="ash"
                                        type="number"
                                        min="0"
                                        max="100"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="erucidicAcidVievs"
                                label="Эруковая кислота:"
                                label-class="required"
                                label-for="create-erucidic-acid"
                                label-cols="4"
                                description="Значение 0 - 20%">
                                <b-input-group append="%">
                                    <b-input
                                        required
                                        id="create-erucidic-acid"
                                        v-model="erucidicAcid"
                                        type="number"
                                        min="0"
                                        max="20"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="peroxideValueVievs"
                                label="Перекисное число:"
                                label-class="required"
                                label-for="create-peroxide-value"
                                label-cols="4"
                                description="Значение 0 - 20%">
                                <b-input-group append="%">
                                    <b-input
                                        required
                                        id="create-peroxide-value"
                                        v-model="peroxideValue"
                                        type="number"
                                        min="0"
                                        max="20"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="acidValueVievs"
                                label="Кислотное число:"
                                label-class="required"
                                label-for="create-acid-value"
                                label-cols="4"
                                description="Значение 0 - 20%">
                                <b-input-group append="%">
                                    <b-input
                                        required
                                        id="create-acid-value"
                                        v-model="acidValue"
                                        type="number"
                                        min="0"
                                        max="20"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="otherColorVievs"
                                label="Другой цвет:"
                                label-class="required"
                                label-for="create-other-color"
                                label-cols="4"
                                description="Значение 1 - 5%">
                                <b-input-group append="%">
                                    <b-input
                                        required
                                        id="create-other-color"
                                        v-model="otherColor"
                                        type="number"
                                        min="1"
                                        max="5"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                label="Год урожая:"
                                label-for="create-crop-year"
                                label-cols="4">
                                <b-input
                                    id="create-crop-year"
                                    v-model="cropYear"
                                    type="text"></b-input>
                            </b-form-group>

                            <b-button
                                variant="secondary"
                                v-on:click="oneStep">Назад</b-button>

                            <b-button
                                variant="primary"
                                v-bind:class="{ 'disabled': !twoStepCompleted }"
                                v-on:click="threeStep">Место поставки</b-button>
                        </div>


                        <!-- STEP 3 -->
                        <div class="orders-step" v-bind:class="{ '_active': step_3 }">

                             <b-form-group label="Basis:">
                                <b-tabs content-class="" justified pills align="center">
                                    <b-tab active v-on:click="basis = 'FOB'" title="FOB">
                                        <p class="small">FOB - загружено на судно в порту отгрузки</p>
                                        <b-form-group
                                            v-if="basis == 'FOB'"
                                            label="Порт:"
                                            label-class="required"
                                            label-for="create-fob-port">
                                            <b-input
                                                required
                                                id="create-fob-port"
                                                v-model="fobPort"
                                                type="text"></b-input>
                                        </b-form-group>
                                        <b-form-group
                                            v-if="basis == 'FOB'"
                                            label="Терминал:"
                                            label-class="required"
                                            label-for="create-fob-terminal">
                                            <b-input
                                                required
                                                id="create-fob-terminal"
                                                v-model="fobTerminal"
                                                type="text"></b-input>
                                        </b-form-group>
                                    </b-tab>
                                    <b-tab v-on:click="basis = 'CIF'" title="CIF">
                                        <p class="small">CIF - доставка в порт назначения</p>
                                        <b-form-group
                                            v-if="basis == 'CIF'"
                                            label="Страна:"
                                            label-class="required"
                                            label-for="create-cif-country">
                                            <b-input
                                                required
                                                id="create-cif-country"
                                                v-model="cifCountry"
                                                type="text"></b-input>
                                        </b-form-group>
                                        <b-form-group
                                            v-if="basis == 'CIF'"
                                            label="Порт:"
                                            label-class="required"
                                            label-for="create-cif-port">
                                            <b-input
                                                required
                                                id="create-cif-port"
                                                v-model="cifPort"
                                                type="text"></b-input>
                                        </b-form-group>
                                    </b-tab>
                                </b-tabs>
                            </b-form-group>

                            <b-form-group
                                label="Период поставки:"
                                label-class="required"
                                label-for="create-period">
                                <b-input
                                    required
                                    id="create-period"
                                    v-model="period"
                                    type="text"></b-input>
                            </b-form-group>

                            <b-form-group
                                label="Дополнительная информация:"
                                label-for="create-text">
                                <b-textarea
                                    id="create-text"
                                    v-model="text"></b-textarea>
                            </b-form-group>

                            <b-button
                                variant="secondary"
                                v-on:click="twoStep">Назад</b-button>
                            <b-button
                                variant="primary"
                                v-bind:class="{ 'disabled': !threeStepCompleted }"
                                v-on:click="onSubmit">Опубликовать</b-button>
                        </div>

                    </b-col>

                    <b-col cols="12" md="4">
                        <h2 class="section_title">Ваше Объявление</h2>

                    </b-col>

                </b-row>
            </b-container>
        </section>
    </main>

</template>



<script>
export default {

    head() {
        return {
            title: 'Market | site.com',
            meta: [{ hid: 'description', name: 'description', content: '' }]
        }
    },

    data() {
        return {
            step_1: true,       // шаг создания объявления
            step_2: false,      // шаг создания объявления
            step_3: false,      // шаг создания объявления

            // данные для шага 1
            deal: 'sell',       // тип объявления (покупка или продажа)
            dealList: [
                {value: 'sell', text: 'Selling'},
                {value: 'buy', text: 'Buying'}
            ],
            cropId: 0,          // ID культуры
            cropsList: {},      // список культур
            currency: 'USD',    // валюта
            currencyList: {},   // список валют
            price: '',          // цена
            quantity: '',       // объем

            // данные для шага 2
            moisture: '',       // влажность - 0-100%
            foreignMatter: '', // сорная примесь - 0-100%
            grainAdmixture: '',// зерновая примесь - 0-100%
            gluten: '',         // клейковина - 12-40%
            protein: '',        // протеин - 0-80%
            naturalWeight: '', // натура - 50-1000 грам/литр
            fallingNumber: '', // число падения - 50-500 штук
            vitreousness: '',   // стекловидность - 20-95%
            ragweed: '',        // амброзия - 0-500 штук/кг
            bug: '',            // клоп - 0-20%
            oilContent: '',    // масличность - 0-80%
            oilAdmixture: '',  // масличная примесь - 0-100%
            broken: '',         // битые - 0-100%
            damaged: '',        // повреждённые - 0-100%
            dirty: '',          // маранные - 0-100%
            ash: '',            // зольность - 0-100%
            erucidicAcid: '',  // эруковая кислота - 0-20%
            peroxideValue: '', // перекисное число - 0-20%
            acidValue: '',     // кислотное число - 0-20%
            otherColor: '',    // другой цвет - 1-5
            cropYear: '',      // год урожая

            // данные для шага 3
            basis: false,       // базис
            fobPort: '',        // базис - порт
            fobTerminal: '',    // базис - терминал
            cifCountry: '',     // базис - страна
            cifPort: '',        // базис - порт
            period: '',         // период поставки
            text: '',           // дополнительная информация
        }
    },

    async asyncData ({ $axios }) {

        let [cropsList, currencyList] = await Promise.all([
            $axios.$get('/api/crop/market-list/index').then((res) => {
                return res;
            }),

            $axios.$get('/api/currency/market-list/index').then((res) => {
                return res;
            }),
        ]);

        let currency = (currencyList[0]) ? currencyList[0].name : false;

        return {
            cropsList: cropsList,
            currencyList: currencyList,
            currency: currency,
        };
    },

    computed: {
        /**
         * проверка, завершен ли первый шаг
         * @return boolean
         */
        firstStepCompleted() {
            return this.checkFirstStep();
        },

        /**
         * проверка, завершен ли второй шаг
         * @return boolean
         */
        twoStepCompleted() {
            return this.checkTwoStep();
        },

        /**
         * проверка, завершен ли третий шаг
         * @return boolean
         */
        threeStepCompleted() {
            return this.checkThreeStep();
        },

        /**
         * зерновая примесь
         * @return boolean
         */
        grainAdmixtureVievs() {
            return ['1','2','3','18','19','20'].indexOf('' + this.cropId) != -1;
        },

        /**
         * клейковина
         * @return boolean
         */
        glutenVievs() {
            return ['1','2'].indexOf('' + this.cropId) != -1;
        },

        /**
         * протеин
         * @return boolean
         */
        proteinVievs() {
            return ['1','2','8','21'].indexOf('' + this.cropId) != -1;
        },

        /**
         * натура
         * @return boolean
         */
        naturalWeightVievs() {
            return ['1','2','3','18','19','21'].indexOf('' + this.cropId) != -1;
        },

        /**
         * число падения
         * @return boolean
         */
        fallingNumberVievs() {
            return ['1','18'].indexOf('' + this.cropId) != -1;
        },

        /**
         * стекловидность
         * @return boolean
         */
        vitreousnessVievs() {
            return ['2'].indexOf('' + this.cropId) != -1;
        },

        /**
         * амброзия
         * @return boolean
         */
        ragweedVievs() {
            return ['4'].indexOf('' + this.cropId) != -1;
        },

        /**
         * клоп
         * @return boolean
         */
        bugVievs() {
            return ['1','2'].indexOf('' + this.cropId) != -1;
        },

        /**
         * масличность
         * @return boolean
         */
        oilContentVievs() {
            return ['5','6','8','9','11','12','16'].indexOf('' + this.cropId) != -1;
        },

        /**
         * масличная примесь
         * @return boolean
         */
        oilAdmixtureVievs() {
            return ['6','9','11'].indexOf('' + this.cropId) != -1;
        },

        /**
         * битые
         * @return boolean
         */
        brokenVievs() {
            return ['4','7','10','15','17','22'].indexOf('' + this.cropId) != -1;
        },

        /**
         * повреждённые
         * @return boolean
         */
        damagedVievs() {
            return ['4','7'].indexOf('' + this.cropId) != -1;
        },

        /**
         * маранные
         * @return boolean
         */
        dirtyVievs() {
            return ['10'].indexOf('' + this.cropId) != -1;
        },

        /**
         * зольность
         * @return boolean
         */
        ashVievs() {
            return ['21'].indexOf('' + this.cropId) != -1;
        },

        /**
         * эруковая кислота
         * @return boolean
         */
        erucidicAcidVievs() {
            return ['6'].indexOf('' + this.cropId) != -1;
        },

        /**
         * перекисное число
         * @return boolean
         */
        peroxideValueVievs() {
            return ['5','6','9'].indexOf('' + this.cropId) != -1;
        },

        /**
         * кислотное число
         * @return boolean
         */
        acidValueVievs() {
            return ['5','6','9'].indexOf('' + this.cropId) != -1;
        },

        /**
         * другой цвет
         * @return boolean
         */
        otherColorVievs() {
            return ['7'].indexOf('' + this.cropId) != -1;
        },

    },

    methods: {
        /**
         * Переход к первому шагу
         */
        oneStep() {
            this.step_1 = true;
            this.step_2 = false;
            this.step_3 = false;
        },

        /**
         * Переход ко второму шагу
         */
        twoStep() {
            if (this.checkFirstStep()) {
                this.step_1 = false;
                this.step_2 = true;
                this.step_3 = false;
            }
        },

        /**
         * Переход к третьему шагу
         */
        threeStep() {
            if (this.checkTwoStep()) {
                this.step_1 = false;
                this.step_2 = false;
                this.step_3 = true;
            }
        },

        /**
         * Проверка заполненых полей на шаге 1
         * @return boolean
         */
        checkFirstStep() {
            return (this.cropId && this.price.length && this.quantity.length) ? true : false;
        },

        /**
         * Проверка заполненых полей на шаге 2
         * @return boolean
         */
        checkTwoStep() {
            if (!this.moisture) {return false;}
            if (!this.foreignMatter) {return false;}

            if (this.grainAdmixtureVievs) {
                if (!this.grainAdmixture) {return false;}
            }
            if (this.glutenVievs) {
                if (!this.gluten) {return false;}
            }
            if (this.proteinVievs) {
                if (!this.protein) {return false;}
            }
            if (this.naturalWeightVievs) {
                if (!this.naturalWeight) {return false;}
            }
            if (this.fallingNumberVievs) {
                if (!this.fallingNumber) {return false;}
            }
            if (this.vitreousnessVievs) {
                if (!this.vitreousness) {return false;}
            }
            if (this.ragweedVievs) {
                if (!this.ragweed) {return false;}
            }
            if (this.bugVievs) {
                if (!this.bug) {return false;}
            }
            if (this.oilContentVievs) {
                if (!this.oilContent) {return false;}
            }
            if (this.oilAdmixtureVievs) {
                if (!this.oilAdmixture) {return false;}
            }
            if (this.brokenVievs) {
                if (!this.broken) {return false;}
            }
            if (this.damagedVievs) {
                if (!this.damaged) {return false;}
            }
            if (this.dirtyVievs) {
                if (!this.dirty) {return false;}
            }
            if (this.ashVievs) {
                if (!this.ash) {return false;}
            }
            if (this.erucidicAcidVievs) {
                if (!this.erucidicAcid) {return false;}
            }
            if (this.peroxideValueVievs) {
                if (!this.peroxideValue) {return false;}
            }
            if (this.acidValueVievs) {
                if (!this.acidValue) {return false;}
            }
            if (this.otherColorVievs) {
                if (!this.otherColor) {return false;}
            }

            return true;
        },

        /**
         * Проверка заполненых полей на шаге 3
         * @return boolean
         */
        checkThreeStep() {
            if (this.basis == 'FOB') {
                if (!this.fobPort) {return false;}
                if (!this.fobTerminal) {return false;}
            }
            if (this.basis == 'CIF') {
                if (!this.cifCountry) {return false;}
                if (!this.cifPort) {return false;}
            }

            if (!this.period) {return false;}

            return true;
        },

        /**
         * Отправка и создание объявления
         * @return {[type]} [description]
         */
        async onSubmit() {
            /**
             * TODO: проверка значений
             */

            var params = new URLSearchParams();
            params.append('deal', this.deal);
            params.append('crop_id', this.cropId);
            params.append('currency', this.currency);
            params.append('price', this.price);
            params.append('quantity', this.quantity);

            params.append('moisture', this.moisture);
            params.append('foreign_matter', this.foreignMatter);
            params.append('grain_admixture', this.grainAdmixture);
            params.append('gluten', this.gluten);
            params.append('protein', this.protein);
            params.append('natural_weight', this.naturalWeight);
            params.append('falling_number', this.fallingNumber);
            params.append('vitreousness', this.vitreousness);
            params.append('ragweed', this.ragweed);
            params.append('bug', this.bug);
            params.append('oil_content', this.oilContent);
            params.append('oil_admixture', this.oilAdmixture);
            params.append('broken', this.broken);
            params.append('damaged', this.damaged);
            params.append('dirty', this.dirty);
            params.append('ash', this.ash);
            params.append('erucidic_acid', this.erucidicAcid);
            params.append('peroxide_value', this.peroxideValue);
            params.append('acid_value', this.acidValue);
            params.append('other_color', this.otherColor);
            params.append('crop_year', this.cropYear);

            params.append('basis', this.basis);
            params.append('fob_port', this.fobPort);
            params.append('fob_terminal', this.fobTerminal);
            params.append('cif_country', this.cifCountry);
            params.append('cif_port', this.cifPort);
            params.append('period', this.period);
            params.append('text', this.text);

            let create = await this.$axios.$post('/api/lot/create/index', params).then((res) => {
                console.log(res);
                return res;
            }).catch((error) => {
                console.log(error);
                return {success:false,error:true};
            });

            /**
             * TODO: обрабатка создания и отправка на страницу
             */
        }
    }
};
</script>



<style lang='scss'>
.orders-steps {
    display: table;
    width: 100%;
    margin: 1.5rem 0;
    border: 1px solid gray;
    border-radius: 3px;
}

.orders-steps-item {
    position: relative;
    display: table-cell;
    width: 33.33%;
    padding: 5px 10px;
    border-left: 1px solid gray;
    color: gray;
    background-color: $light;

    &:first-child {
        border-left: 0;
    }

    &._active {
        color: $body-color;
        background-color: $white;

        .orders-steps-item-link {
            color: $primary;
        }
    }
}

.orders-steps-item-link {
    cursor: pointer;

    &:hover {
        text-decoration: underline;
    }
}

.orders-step {
    display: none;

    &._active {
        display: block;
    }
}

.input-group {
    > .input-group-prepend > .custom-select  {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
}
</style>
