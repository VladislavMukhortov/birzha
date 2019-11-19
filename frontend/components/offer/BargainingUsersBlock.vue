// Блок с запросами на подтверждение "твердо" и с "твердо"
// можно дать твердо, если нет других офферов со статусом "твердо"
// так же показывает уведомления о взаимодействии

<template>

    <div>

        <h5 class="text-danger">Твердо до {{ offer.ended_at }}</h5>



        <!-- создавший объявление -->
        <template v-if="offer.lot_owner">
            <div class="text-info">Объявление ваше</div>
        </template>
        <!-- запросивший твердо (контрагент) -->
        <template v-else>
            <div class="text-info">Вы запросили твердо</div>
        </template>



        <hr>



        <!-- Блок с ценами аукциона -->
        <template v-if="offer.price && offer.price.require_1">

            <div v-if="offer.price.require_1" v-text="offer.price.require_1"></div>
            <div v-if="offer.price.lot_1" v-text="offer.price.lot_1"></div>

            <div v-if="offer.price.require_2" v-text="offer.price.require_2"></div>
            <div v-if="offer.price.lot_2" v-text="offer.price.lot_2"></div>

            <div v-if="offer.price.require_3" v-text="offer.price.require_3"></div>

            <hr>

        </template>



        <!-- создавший объявление -->
        <template v-if="offer.lot_owner">
            <!-- Цену вводит создавший объявление в ответ на пониженный|повышенный запрос цены -->
            <template v-if="offer.price_offer == 'owner'">

                <p>
                    <b-button
                        variant="success"
                        v-on:click="acceptPrice">Принять цену контрагента</b-button>
                </p>

                <p>Если вас не устраивает цена, укажите свою цену</p>

                <b-form-group>
                    <b-input-group>
                        <b-input-group-prepend>
                            <div class="input-group-text">{{ offer.currency }}</div>
                        </b-input-group-prepend>
                        <b-input required type="text" v-model="newPrice"></b-input>
                        <b-input-group-append>
                            <b-button variant="primary" v-on:click="sendNewPrice">Установить</b-button>
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>

            </template>
            <!-- Последний ход аукциона, либо отмена либо согласие на цену -->
            <template v-else-if="offer.price_offer == 'last'">

                <b-button
                    variant="success"
                    v-on:click="acceptPrice">Принять цену контрагента</b-button>

                <b-button
                    variant="danger"
                    v-on:click="cancelPrice">Отказаться</b-button>

            </template>
            <!--  -->
            <template v-else>
                <div class="text-muted">Ожидайте решения второй стороны</div>
            </template>
        </template>
        <!-- запросивший твердо (контрагент) -->
        <template v-else>
            <!-- Цену вводит контрагент -->
            <template v-if="offer.price_offer == 'counterparty'">

                <p>
                    <b-button
                        variant="success"
                        v-on:click="acceptPrice">Принять цену объявления</b-button>
                </p>

                <p class="text-muted">У вас есть 3 попытки предложить свою цену второй стороне</p>

                <p v-if="offer.deal == 'sell'">Можете попытаться Bid (понизить) цену</p>
                <p v-else>Вы можете попытаться Push (повысить) цену</p>

                <b-form-group>
                    <b-input-group>
                        <b-input-group-prepend>
                            <div class="input-group-text">{{ offer.currency }}</div>
                        </b-input-group-prepend>
                        <b-input required type="text" v-model="newPrice"></b-input>
                        <b-input-group-append>
                            <b-button variant="primary" v-on:click="sendNewPrice">Предложить</b-button>
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>

            </template>
            <template v-else>
                <div class="text-muted">Ожидайте решения второй стороны</div>
            </template>
        </template>



        <pre>{{ offer }}</pre>

    </div>

</template>




<script>
export default {
    props: {
        offer: {
            type: Object
        },
    },

    data() {
        return {
            newPrice: '',    // цена для аукциона
        }
    },

    methods: {
        /**
         * Принимаем начальную цену
         */
        async acceptPrice() {
            var _param = new URLSearchParams();
            _param.append('link', this.offer.link);

            let res = await this.$axios.$post('/api/offer/update/accept-price', _param).then((res) => {
                return res;
            }).catch((error) => {
                return {
                    result: 'error',
                    messages: 'При сохранении возникла ошибка, попробуйте позже',
                };
            });

            if (res.result == 'success') {
                this.$router.push('/deals/communication');
            }
        },



        /**
         * Принимаем начальную цену
         */
        async cancelPrice() {
            var _param = new URLSearchParams();
            _param.append('link', this.offer.link);

            let res = await this.$axios.$post('/api/offer/update/cancel-price', _param).then((res) => {
                return res;
            }).catch((error) => {
                return {
                    result: 'error',
                    messages: 'При сохранении возникла ошибка, попробуйте позже',
                };
            });

            if (res.result == 'success') {
                this.$router.push('/deals/auction');
            }
        },



        /**
         * Устанавливаем цену в аукционе между пользователями
         */
        async sendNewPrice() {
            var _param = new URLSearchParams();
            _param.append('link', this.offer.link);
            _param.append('price', this.newPrice);

            let res = await this.$axios.$post('/api/offer/update/new-price', _param).then((res) => {
                return res;
            }).catch((error) => {
                return {
                    result: 'error',
                    messages: ['При сохранении возникла ошибка, попробуйте позже'],
                };
            });

            if (res.result == 'success') {
                this.offer.price = res.offer.price;
                this.offer.price_offer = res.offer.price_offer;
            } else {
            }
        },
    },
};
</script>



<style lang='scss'>
</style>
