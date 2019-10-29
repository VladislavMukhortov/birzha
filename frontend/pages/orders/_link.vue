// Страница со своим объявлением
// можно редактировать
// посмотреть запросы "твердо" и подтвердить запрос

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row>
                    <b-col cols="12" md="10" lg="8">

                        <h1 class="section_title">My orders</h1>

                        <template v-if="offers.length">

                            <h2>На ваше объявление запросили "твердо"</h2>

                            <b-list-group>
                                <b-list-group-item v-for="item in offers" v-bind:key="item.link">
                                    <span>#{{ item.id }}</span>
                                    <span>Запросили: {{ item.created_at }}</span>
                                    <span>{{ item.desc }}</span>
                                    <template v-if="item.status">
                                        <b-link to="">Перейти</b-link>
                                    </template>
                                    <template v-else>
                                        <b-button variant="primary" v-on:click="startAuction(item.link)">Дать твердо</b-button>
                                    </template>
                                </b-list-group-item>
                            </b-list-group>

                        </template>

                        <h2>Редактирование объявления</h2>

                        <b-alert
                            variant="success"
                            dismissible
                            fade
                            v-bind:show="alertSuccessShow"
                            v-on:dismissed="alertSuccessShow=false">Объявление успешно изменено.</b-alert>

                        <b-alert
                            variant="danger"
                            dismissible
                            fade
                            v-bind:show="alertErrorShow"
                            v-on:dismissed="alertErrorShow=false">
                            <div v-for="(value, name) in errorMessages">{{ value }}</div>
                        </b-alert>

                        <b-alert
                            variant="danger"
                            v-bind:show="cantEdit">Объявление нельзя редактировать пока оно в статусе "твердо"</b-alert>

                        <div>
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

                                    <b-input type="text" v-model="price" v-bind:readonly="cantEdit"></b-input>

                                </b-input-group>
                            </b-form-group>

                            <b-form-group label-class="required" label="Укажите объём тонн:">
                                <b-input type="text" v-model="quantity" v-bind:readonly="cantEdit"></b-input>
                            </b-form-group>

                            <b-form-group
                                label="Влажность:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="100" v-model="moisture" v-bind:readonly="cantEdit"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                label="Сорная примесь:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="100" v-model="foreignMatter" v-bind:readonly="cantEdit"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="wVievs"
                                label="W:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 1000w">
                                <b-input-group append="w">
                                    <b-input type="number" min="0" max="1000" v-model="w" v-bind:readonly="cantEdit"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="grainAdmixtureVievs"
                                label="Зерновая примесь:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="100" v-model="grainAdmixture" v-bind:readonly="cantEdit"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="glutenVievs"
                                label="Клейковина:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 12 - 40%">
                                <b-input-group append="%">
                                    <b-input type="number" min="12" max="40" v-model="gluten" v-bind:readonly="cantEdit"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="proteinVievs"
                                label="Протеин:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 80%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="80" v-model="protein" v-bind:readonly="cantEdit"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="naturalWeightVievs"
                                label="Натура:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 50 - 1000 грам/литр">
                                <b-input-group append="грам/литр">
                                    <b-input type="number" min="50" max="1000" v-model="naturalWeight" v-bind:readonly="cantEdit"></b-input>
                                    </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="fallingNumberVievs"
                                label="Число падения:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 50 - 500 штук">
                                <b-input-group append="штук">
                                    <b-input type="number" min="50" max="500" v-model="fallingNumber" v-bind:readonly="cantEdit"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="vitreousnessVievs"
                                label="Стекловидность:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 20 - 95%">
                                <b-input-group append="%">
                                    <b-input type="number" min="20" max="95" v-model="vitreousness" v-bind:readonly="cantEdit"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="ragweedVievs"
                                label="Амброзия:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 500 штук/кг">
                                <b-input-group append="штук/кг">
                                    <b-input type="number" min="0" max="500" v-model="ragweed" v-bind:readonly="cantEdit"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="bugVievs"
                                label="Клоп:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 20%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="20" v-model="bug" v-bind:readonly="cantEdit"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="oilContentVievs"
                                label="Масличность:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 80%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="80" v-model="oilContent" v-bind:readonly="cantEdit"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="oilAdmixtureVievs"
                                label="Масличная примесь:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="100" v-model="oilAdmixture" v-bind:readonly="cantEdit"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="brokenVievs"
                                label="Битые:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="100" v-model="broken" v-bind:readonly="cantEdit"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="damagedVievs"
                                label="Повреждённые:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="100" v-model="damaged" v-bind:readonly="cantEdit"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="dirtyVievs"
                                label="Маранные:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="100" v-model="dirty" v-bind:readonly="cantEdit"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="ashVievs"
                                label="Зольность:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 100%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="100" v-model="ash" v-bind:readonly="cantEdit"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="erucidicAcidVievs"
                                label="Эруковая кислота:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 20%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="20" v-model="erucidicAcid" v-bind:readonly="cantEdit"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="peroxideValueVievs"
                                label="Перекисное число:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 20%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="20" v-model="peroxideValue" v-bind:readonly="cantEdit"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="acidValueVievs"
                                label="Кислотное число:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 0 - 20%">
                                <b-input-group append="%">
                                    <b-input type="number" min="0" max="20" v-model="acidValue" v-bind:readonly="cantEdit"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group
                                v-if="otherColorVievs"
                                label="Другой цвет:"
                                label-class="required"
                                label-cols="4"
                                description="Значение 1 - 5">
                                <b-input-group>
                                    <b-input type="number" min="1" max="5" v-model="otherColor" v-bind:readonly="cantEdit"></b-input>
                                </b-input-group>
                            </b-form-group>

                            <b-form-group label-cols="4" label="Год урожая:">
                                <b-input type="text" v-model="cropYear" v-bind:readonly="cantEdit"></b-input>
                            </b-form-group>

                            <b-form-group label="Basis:">
                                <b-tabs justified pills align="center">
                                    <b-tab v-bind:active="basis == 'FOB'" v-on:click="basis = 'FOB'" title="FOB">
                                        <p class="small">FOB - загружено на судно в порту отгрузки</p>
                                        <b-form-group label-class="required" label="Порт:">
                                            <b-input type="text" v-model="fobPort" v-bind:readonly="cantEdit"></b-input>
                                        </b-form-group>
                                        <b-form-group label-class="required" label="Терминал:">
                                            <b-input type="text" v-model="fobTerminal" v-bind:readonly="cantEdit"></b-input>
                                        </b-form-group>
                                    </b-tab>
                                    <b-tab v-bind:active="basis == 'CIF'" v-on:click="basis = 'CIF'" title="CIF">
                                        <p class="small">CIF - доставка в порт назначения</p>
                                        <b-form-group label-class="required" label="Страна:">
                                            <b-input type="text" v-model="cifCountry" v-bind:readonly="cantEdit"></b-input>
                                        </b-form-group>
                                        <b-form-group label-class="required" label="Порт:">
                                            <b-input type="text" v-model="cifPort" v-bind:readonly="cantEdit"></b-input>
                                        </b-form-group>
                                    </b-tab>
                                </b-tabs>
                            </b-form-group>

                            <b-form-group label-class="required" label="Период поставки:">
                                <b-input type="text" v-model="period" v-bind:readonly="cantEdit"></b-input>
                            </b-form-group>

                            <b-form-group label="Дополнительная информация:">
                                <b-textarea rows="3" v-model="text" v-bind:readonly="cantEdit"></b-textarea>
                            </b-form-group>

                            <b-button
                                variant="primary"
                                v-on:click="onSubmit">Сохранить</b-button>

                        </div>

                        <pre>{{ offers }}</pre>

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
            title: ' Edit order | site.com',
        }
    },

    validate({ params }) {
        return /^[\w\-]+$/.test(params.link)
    },

    async fetch ({ $axios, store }) {
        let cropList = await $axios.$get('/api/crop/list/market').then((res) => {
            return res;
        });
        store.commit('lot/SET_CROP_LIST', cropList);
    },

    /**
     * Параметры в объекте, что бы избежать ошибок при 404
     */
    data() {
        return {
            offers: {},         // информация об объявлении
            cantEdit: true,     // нельзя редактировать объявление

            deal: '',           // тип объявления (покупка или продажа)
            cropId: 0,          // ID культуры
            currency: '',       // валюта
            price: '',          // цена
            quantity: '',       // объем

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

            basis: '',          // базис
            fobPort: '',        // базис - порт
            fobTerminal: '',    // базис - терминал
            cifCountry: '',     // базис - страна
            cifPort: '',        // базис - порт
            period: '',         // период поставки
            text: '',           // дополнительная информация

            // уведомления для сохранения объявления
            alertSuccessShow: false,    // уведомление о успешном изменении
            alertErrorShow: false,      // уведомление об ошибке при изменении
            errorMessages: [],          // сообщения об ошибке
        }
    },

    /**
     * получаем данные о объявлении
     * @return object
     */
    async asyncData({ $axios, params }) {
        let _param = {params: {
            link: params.link
        }};

        let [lot, offers] = await Promise.all([
            $axios.$get('/api/lot/show/my-order', _param).then((res) => {
                return res;
            }),

            $axios.$get('/api/offer/show/my-order', _param).then((res) => {
                return res;
            })
        ]);

        // объявления нет, редиректим на 404
        if (lot.result == 'error') {
            $nuxt.$router.push('/orders/404');
            return;
        }

        return {
            cantEdit: lot.data.cant_edit,

            deal: lot.data.deal,
            cropId: lot.data.crop_id,
            currency: lot.data.currency,
            price: lot.data.price,
            quantity: lot.data.quantity,

            moisture: lot.data.moisture,
            foreignMatter: lot.data.foreign_matter,
            w: lot.data.w,
            grainAdmixture: lot.data.grain_admixture,
            gluten: lot.data.gluten,
            protein: lot.data.protein,
            naturalWeight: lot.data.natural_weight,
            fallingNumber: lot.data.falling_number,
            vitreousness: lot.data.vitreousness,
            ragweed: lot.data.ragweed,
            bug: lot.data.bug,
            oilContent: lot.data.oil_content,
            oilAdmixture: lot.data.oil_admixture,
            broken: lot.data.broken,
            damaged: lot.data.damaged,
            dirty: lot.data.dirty,
            ash: lot.data.ash,
            erucidicAcid: lot.data.erucidic_acid,
            peroxideValue: lot.data.peroxide_value,
            acidValue: lot.data.acid_value,
            otherColor: lot.data.other_color,
            cropYear: lot.data.crop_year,

            basis: lot.data.basis,
            fobPort: lot.data.fob_port,
            fobTerminal: lot.data.fob_terminal,
            cifCountry: lot.data.cif_country,
            cifPort: lot.data.cif_port,
            period: lot.data.period,
            text: lot.data.text,

            offers: offers.data,
        };
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
        startAuction(link) {
            console.log(link);
        },

        /**
         * Отправка и создание объявления
         * @return {[type]} [description]
         */
        async onSubmit() {
            this.alertSuccessShow = false;
            this.alertErrorShow = false;

            var params = new URLSearchParams();
            params.append('link', this.$route.params.link);

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

            let res = await this.$axios.$post('/api/lot/edit/index', params).then((res) => {
                return res;
            }).catch((error) => {
                return {
                    result: 'error',
                    messages: ['При сохранении возникла ошибка, попробуйте позже'],
                };
            });

            if (res.result == 'success') {
                /**
                 * TODO: как то заблокировать даблклик
                 * @type {Boolean}
                 */
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
</style>
