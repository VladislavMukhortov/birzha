// Блок с запросами на подтверждение "твердо" и с "твердо"
// можно дать твердо, если нет других офферов со статусом "твердо"
// так же показывает уведомления о взаимодействии

<template>

    <div>

        <h5 class="text-danger">Твердо до {{ offer.ended_at }}</h5>



        <!-- создавший объявление -->
        <template v-if="offer.lot_owner">
            <p class="text-info">Объявление ваше</p>
        </template>
        <!-- запросивший твердо (контрагент) -->
        <template v-else>
            <p class="text-info">Вы запросили твердо</p>
            <b-button
                variant="success"
                v-on:click="sendNewPrice">Согласиться на начальную цену</b-button>
        </template>



        <hr>



        <template v-if="offer.price && offer.price.require_1">

            <template v-if="offer.price.require_1">
                <p>Запросили цену: {{ offer.price.require_1 }}</p>
            </template>
            <template v-if="offer.price.lot_1">
                <p>Поставил цену: {{ offer.price.lot_1 }}</p>
            </template>

            <template v-if="offer.price.require_2">
                <p>Запросили цену: {{ offer.price.require_2 }}</p>
            </template>
            <template v-if="offer.price.lot_2">
                <p>Поставил цену: {{ offer.price.lot_2 }}</p>
            </template>

            <template v-if="offer.price.require_3">
                <p>Запросили цену: {{ offer.price.require_3 }}</p>
            </template>

            <hr>

        </template>


        <!-- создавший объявление -->
        <template v-if="offer.lot_owner">
            <!-- Цену вводит создавший объявление в ответ на пониженный|повышенный запрос цены -->
            <template v-if="offer.price_offer == 'owner'">
                <p>Если вас не устраивает цена, укажите свою цену</p>
                <b-form-group>
                    <b-input-group>
                        <b-input-group-prepend>
                            <div class="input-group-text">{{ offer.currency }}</div>
                        </b-input-group-prepend>
                        <b-input required type="text" v-model="newPrice"></b-input>
                        <b-input-group-append>
                            <b-button variant="primary" v-on:click="sendNewPrice">
                                <span v-if="offer.deal == 'sell'">Bid (понизить) цену</span>
                                <span v-else>Push (повысить) цену</span>
                            </b-button>
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>
            </template>
            <template v-else>
                <div class="text-muted">Контрагент получил ваше "твердо". Ожидайте</div>
            </template>

            <!-- Последний ход аукциона, либо отмена либо согласие на цену -->
            <template v-if="offer.price_offer == 'last'">

            </template>
        </template>
        <!-- запросивший твердо (контрагент) -->
        <template v-else>
            <!-- Цену вводит контрагент -->
            <template v-if="offer.price_offer == 'counterparty'">

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
         * @return {[type]} [description]
         */
        acceptPrice() {

        },

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
