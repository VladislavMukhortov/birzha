<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row>

                    <b-col cols="12" md="8">

                        <h1 class="section_title">Новая заявка</h1>

                        <b-alert
                            variant="success"
                            dismissible
                            fade
                            v-bind:show="alertSuccessShow"
                            v-on:dismissed="alertSuccessShow=false">
                            <p>Объявление успешно размещено.</p>
                            <div>
                                <b-link class="btn btn-primary" to="/orders">My orders</b-link>
                                <b-link class="btn btn-primary" to="/market">Market</b-link>
                            </div>
                        </b-alert>

                        <b-alert
                            variant="danger"
                            dismissible
                            fade
                            v-bind:show="alertErrorShow"
                            v-on:dismissed="alertErrorShow=false">
                            <div v-for="(value, name) in errorMessages">{{ value }}</div>
                        </b-alert>

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

                            <b-form-group label-class="required" label="Выберите культуру:">
                                <b-select v-model="cropId">
                                    <option v-bind:value="0" disabled>Наименование товара</option>
                                    <template v-for="crop_item in cropList">
                                        <option v-bind:value="crop_item.id">{{ crop_item.name }}</option>
                                    </template>
                                </b-select>
                            </b-form-group>

                            <b-form-group label-class="required" label="Валюта и цена за тонну:">
                                <b-input-group>

                                    <b-input-group-prepend>
                                        <b-select v-model="currency">
                                            <template v-for="currency_item in currencyList">
                                                <option v-bind:value="currency_item.name">{{ currency_item.name }}</option>
                                            </template>
                                        </b-select>
                                    </b-input-group-prepend>

                                    <b-input type="text" v-model="price"></b-input>

                                </b-input-group>
                            </b-form-group>

                            <b-form-group label-class="required" label="Укажите объём тонн:">
                                <b-input type="text" v-model="quantity"></b-input>
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
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="100" v-model="moisture"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                label="Сорная примесь:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="100" v-model="foreignMatter"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="wVievs"
                                label="W:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 1000w">
                                <b-input-group append="w">
                                    <b-input type="number" min="0" max="1000" v-model="w"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="grainAdmixtureVievs"
                                label="Зерновая примесь:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="100" v-model="grainAdmixture"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="glutenVievs"
                                label="Клейковина:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 12 - 40%">
                                <b-input-group append="%">
                                    <b-input type="number" min="12" max="40" v-model="gluten"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="proteinVievs"
                                label="Протеин:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 80%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="80" v-model="protein"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="naturalWeightVievs"
                                label="Натура:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 50 - 1000 грам/литр">
                                <b-input-group append="грам/литр">
                                    <b-input type="number" min="50" max="1000" v-model="naturalWeight"></b-input>
                                    </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="fallingNumberVievs"
                                label="Число падения:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 50 - 500 штук">
                                <b-input-group append="штук">
                                    <b-input type="number" min="50" max="500" v-model="fallingNumber"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="vitreousnessVievs"
                                label="Стекловидность:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 20 - 95%">
                                <b-input-group append="%">
                                    <b-input type="number" min="20" max="95" v-model="vitreousness"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="ragweedVievs"
                                label="Амброзия:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 500 штук/кг">
                                <b-input-group append="штук/кг">
                                    <b-input type="number" min="0" max="500" v-model="ragweed"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="bugVievs"
                                label="Клоп:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 20%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="20" v-model="bug"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="oilContentVievs"
                                label="Масличность:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 80%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="80" v-model="oilContent"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="oilAdmixtureVievs"
                                label="Масличная примесь:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="100" v-model="oilAdmixture"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="brokenVievs"
                                label="Битые:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="100" v-model="broken"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="damagedVievs"
                                label="Повреждённые:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="100" v-model="damaged"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="dirtyVievs"
                                label="Маранные:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="100" v-model="dirty"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="ashVievs"
                                label="Зольность:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="100" v-model="ash"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="erucidicAcidVievs"
                                label="Эруковая кислота:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 20%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="20" v-model="erucidicAcid"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="peroxideValueVievs"
                                label="Перекисное число:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 20%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="20" v-model="peroxideValue"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="acidValueVievs"
                                label="Кислотное число:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 20%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="20" v-model="acidValue"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="otherColorVievs"
                                label="Другой цвет:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 1 - 5">
                                <b-input-group>
                                    <b-input type="number" min="1" max="5" v-model="otherColor"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group label-cols="4" label="Год урожая:">
                                <b-input type="text" v-model="cropYear"></b-input>
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
                                        <b-form-group v-if="basis == 'FOB'" label-class="required" label="Порт:">
                                            <b-input type="text" v-model="fobPort"></b-input>
                                        </b-form-group>
                                        <b-form-group v-if="basis == 'FOB'" label-class="required" label="Терминал:">
                                            <b-input type="text" v-model="fobTerminal"></b-input>
                                        </b-form-group>
                                    </b-tab>
                                    <b-tab v-on:click="basis = 'CIF'" title="CIF">
                                        <p class="small">CIF - доставка в порт назначения</p>
                                        <b-form-group v-if="basis == 'CIF'" label-class="required" label="Страна:">
                                            <b-input type="text" v-model="cifCountry"></b-input>
                                        </b-form-group>
                                        <b-form-group v-if="basis == 'CIF'" label-class="required" label="Порт:">
                                            <b-input type="text" v-model="cifPort"></b-input>
                                        </b-form-group>
                                    </b-tab>
                                </b-tabs>
                            </b-form-group>

                            <b-form-group label-class="required" label="Период поставки:">
                                <b-input type="text" v-model="period"></b-input>
                            </b-form-group>

                            <b-form-group label="Дополнительная информация:">
                                <b-textarea rows="3" v-model="text"></b-textarea>
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
                        <p>{{ lotVievsDeal }}</p>
                        <p>{{ lotVievsCropName }}</p>
                        <p>{{ lotVievsPrice }}</p>
                        <p>{{ lotVievsBasis }}</p>
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
            cropId: 0,          // ID культуры
            currency: 'USD',    // валюта
            price: '',          // цена
            quantity: '',       // объем

            // данные для шага 2
            moisture: '',       // влажность - 0-100%
            foreignMatter: '',  // сорная примесь - 0-100%
            w: '',              // w - 0-1000w
            grainAdmixture: '', // зерновая примесь - 0-100%
            gluten: '',         // клейковина - 12-40%
            protein: '',        // протеин - 0-80%
            naturalWeight: '',  // натура - 50-1000 грам/литр
            fallingNumber: '',  // число падения - 50-500 штук
            vitreousness: '',   // стекловидность - 20-95%
            ragweed: '',        // амброзия - 0-500 штук/кг
            bug: '',            // клоп - 0-20%
            oilContent: '',     // масличность - 0-80%
            oilAdmixture: '',   // масличная примесь - 0-100%
            broken: '',         // битые - 0-100%
            damaged: '',        // повреждённые - 0-100%
            dirty: '',          // маранные - 0-100%
            ash: '',            // зольность - 0-100%
            erucidicAcid: '',   // эруковая кислота - 0-20%
            peroxideValue: '',  // перекисное число - 0-20%
            acidValue: '',      // кислотное число - 0-20%
            otherColor: '',     // другой цвет - 1-5
            cropYear: '',       // год урожая

            // данные для шага 3
            basis: 'FOB',       // базис
            fobPort: '',        // базис - порт
            fobTerminal: '',    // базис - терминал
            cifCountry: '',     // базис - страна
            cifPort: '',        // базис - порт
            period: '',         // период поставки
            text: '',           // дополнительная информация

            // уведомления для сохранения объявления
            alertSuccessShow: false,    // уведомление о успешном размещении
            alertErrorShow: false,      // уведомление об ошибке при размещении
            errorMessages: [],          // сообщения об ошибке
        }
    },

    mounted() {
        this.$store.dispatch('lot/GET_CROP_LIST');
    },

    computed: {
        /**
         * тип объявления (покупка или продажа)
         * @return array
         */
        dealList() {
            return this.$store.state.lot.dealList;
        },

        /**
         * список культур
         * @return array
         */
        cropList() {
            return this.$store.state.lot.cropList;
        },

        /**
         * список валют
         * @return array
         */
        currencyList() {
            return this.$store.state.lot.currencyList;
        },

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
         * w - проверка, отображать ли поле для ввода
         * @return boolean
         */
        wVievs() {
            return ['1','2'].indexOf('' + this.cropId) != -1;
        },

        /**
         * зерновая примесь - проверка, отображать ли поле для ввода
         * @return boolean
         */
        grainAdmixtureVievs() {
            return ['1','2','3','18','19','20'].indexOf('' + this.cropId) != -1;
        },

        /**
         * клейковина - проверка, отображать ли поле для ввода
         * @return boolean
         */
        glutenVievs() {
            return ['1','2'].indexOf('' + this.cropId) != -1;
        },

        /**
         * протеин - проверка, отображать ли поле для ввода
         * @return boolean
         */
        proteinVievs() {
            return ['1','2','8','21'].indexOf('' + this.cropId) != -1;
        },

        /**
         * натура - проверка, отображать ли поле для ввода
         * @return boolean
         */
        naturalWeightVievs() {
            return ['1','2','3','18','19','21'].indexOf('' + this.cropId) != -1;
        },

        /**
         * число падения - проверка, отображать ли поле для ввода
         * @return boolean
         */
        fallingNumberVievs() {
            return ['1','18'].indexOf('' + this.cropId) != -1;
        },

        /**
         * стекловидность - проверка, отображать ли поле для ввода
         * @return boolean
         */
        vitreousnessVievs() {
            return ['2'].indexOf('' + this.cropId) != -1;
        },

        /**
         * амброзия - проверка, отображать ли поле для ввода
         * @return boolean
         */
        ragweedVievs() {
            return ['4'].indexOf('' + this.cropId) != -1;
        },

        /**
         * клоп - проверка, отображать ли поле для ввода
         * @return boolean
         */
        bugVievs() {
            return ['1','2'].indexOf('' + this.cropId) != -1;
        },

        /**
         * масличность - проверка, отображать ли поле для ввода
         * @return boolean
         */
        oilContentVievs() {
            return ['5','6','8','9','11','12','16'].indexOf('' + this.cropId) != -1;
        },

        /**
         * масличная примесь - проверка, отображать ли поле для ввода
         * @return boolean
         */
        oilAdmixtureVievs() {
            return ['6','9','11'].indexOf('' + this.cropId) != -1;
        },

        /**
         * битые - проверка, отображать ли поле для ввода
         * @return boolean
         */
        brokenVievs() {
            return ['4','7','10','15','17','22'].indexOf('' + this.cropId) != -1;
        },

        /**
         * повреждённые - проверка, отображать ли поле для ввода
         * @return boolean
         */
        damagedVievs() {
            return ['4','7'].indexOf('' + this.cropId) != -1;
        },

        /**
         * маранные - проверка, отображать ли поле для ввода
         * @return boolean
         */
        dirtyVievs() {
            return ['10'].indexOf('' + this.cropId) != -1;
        },

        /**
         * зольность - проверка, отображать ли поле для ввода
         * @return boolean
         */
        ashVievs() {
            return ['21'].indexOf('' + this.cropId) != -1;
        },

        /**
         * эруковая кислота - проверка, отображать ли поле для ввода
         * @return boolean
         */
        erucidicAcidVievs() {
            return ['6'].indexOf('' + this.cropId) != -1;
        },

        /**
         * перекисное число - проверка, отображать ли поле для ввода
         * @return boolean
         */
        peroxideValueVievs() {
            return ['5','6','9'].indexOf('' + this.cropId) != -1;
        },

        /**
         * кислотное число - проверка, отображать ли поле для ввода
         * @return boolean
         */
        acidValueVievs() {
            return ['5','6','9'].indexOf('' + this.cropId) != -1;
        },

        /**
         * другой цвет - проверка, отображать ли поле для ввода
         * @return boolean
         */
        otherColorVievs() {
            return ['7'].indexOf('' + this.cropId) != -1;
        },

        /**
         * Заполнение данных формы
         * @return string
         */
        lotVievsDeal() {
            let deal = this.deal || '';
            return (deal) ? `Тип объявления: ${deal}` : '';
        },

        /**
         * Заполнение данных формы
         * @return string
         */
        lotVievsCropName() {
            let cropItem = this.cropList.find(item => item.id == this.cropId);
            return (cropItem) ? `Культура: ${cropItem.name}` : '';
        },

        /**
         * Заполнение данных формы
         * @return string
         */
        lotVievsPrice() {
            let price = (this.price) ? `${this.price} ${this.currency}` : '';
            return (this.quantity) ? `${price} / ${this.quantity} тонн` : `${price}`;
        },

        /**
         * Заполнение данных формы
         * @return string
         */
        lotVievsBasis() {
            let basis = '';
            if (this.basis == 'FOB') {
                basis = (this.fobTerminal) ? `${this.fobPort}, ${this.fobTerminal}` : `${this.fobPort}`;
            }
            if (this.basis == 'CIF') {
                basis = (this.cifPort) ? `${this.cifCountry}, ${this.cifPort}` : `${this.cifCountry}`;
            }
            return (basis) ? `${this.basis} - ${basis}` : '';
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

            if (this.wVievs) {
                if (!this.w) {return false;}
            }
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
            this.alertSuccessShow = false;
            this.alertErrorShow = false;

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
            params.append('w', this.w);
            params.append('crop_year', this.cropYear);

            params.append('basis', this.basis);
            params.append('fob_port', this.fobPort);
            params.append('fob_terminal', this.fobTerminal);
            params.append('cif_country', this.cifCountry);
            params.append('cif_port', this.cifPort);
            params.append('period', this.period);
            params.append('text', this.text);

            let res = await this.$axios.$post('/api/lot/create/index', params).then((res) => {
                return res;
            }).catch((error) => {
                return {
                    result: 'error',
                    messages: ['При сохранении возникла ошибка, попробуйте позже'],
                };
            });

            if (res.result == 'success') {
                this.step_1 = false;
                this.step_2 = false;
                this.step_3 = false;
                this.price = '';
                this.quantity = '';
                this.moisture = '';
                this.foreignMatter = '';
                this.w = '';
                this.grainAdmixture = '';
                this.gluten = '';
                this.protein = '';
                this.naturalWeight = '';
                this.fallingNumber = '';
                this.vitreousness = '';
                this.ragweed = '';
                this.bug = '';
                this.oilContent = '';
                this.oilAdmixture = '';
                this.broken = '';
                this.damaged = '';
                this.dirty = '';
                this.ash = '';
                this.erucidicAcid = '';
                this.peroxideValue = '';
                this.acidValue = '';
                this.otherColor = '';
                this.cropYear = '';
                this.basis = '';
                this.fobPort = '';
                this.fobTerminal = '';
                this.cifCountry = '';
                this.cifPort = '';
                this.period = '';
                this.text = '';
                this.alertSuccessShow = true;
            } else {
                this.errorMessages = res.messages;
                this.alertErrorShow = true;
            }

            window.scrollTo(0,0);
        },
    },
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
