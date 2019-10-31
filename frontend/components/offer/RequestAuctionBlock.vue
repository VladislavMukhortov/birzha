// Блок с запросами на подтверждение "твердо" и с "твердо"
// можно дать твердо, если нет других офферов со статусом "твердо"
// так же показывает уведомления о взаимодействии

<template>

    <div>

        <template v-if="offers.length">
            <h2>На ваше объявление запросили "твердо"</h2>


            <!-- Успешно дали "твердо" -->
            <b-alert
                variant="success"
                fade
                v-bind:show="alertSuccessShow">

                <div>{{ alertMessages }}</div>

                <b-link
                    v-bind:to="{name: 'deals-auction-link', params: {link: alertMessagesLink}}"
                    class="btn btn-success">Перейти в сделку</b-link>

                <b-link
                    to="/deals/auction"
                    class="btn btn-primary">Список сделок</b-link>

            </b-alert>


            <!-- При подаче "твердо" позникла ошибка -->
            <b-alert
                variant="danger"
                dismissible
                fade
                v-bind:show="alertErrorShow"
                v-on:dismissed="alertErrorShow=false">{{ alertMessages }}</b-alert>


            <!-- Список запросов на подтверждение "твердо" и с "твердо" -->
            <b-list-group v-if="!alertSuccessShow">
                <b-list-group-item v-for="(item, index) in offers" v-bind:key="item.link">
                    <span>#{{ index+1 }}</span>
                    <span>Запросили: {{ item.created_at }}</span>

                    <template v-if="item.status">
                        <b-link class="btn btn-success" v-bind:to="'/deals/auction/'+item.link">Перейти в сделку</b-link>
                        <span>{{ item.ended_at }}</span>
                    </template>

                    <template v-else>
                        <select v-on:click="setTimeToOffer($event, index)" v-on:change="setTimeToOffer($event, index)">
                            <option v-bind:value="30" :selected="true">30 min</option>
                            <option v-bind:value="60" :selected="false">1 hour</option>
                            <option v-bind:value="120" :selected="false">2 hour</option>
                        </select>
                        <b-button variant="primary" v-on:click="startAuction(item.link, index)">Дать твердо</b-button>
                    </template>

                </b-list-group-item>
            </b-list-group>

        </template>
    </div>

</template>




<script>
export default {
    data() {
        return {
            alertSuccessShow: false,    // уведомление о успешном подтверждении "твердо"
            alertErrorShow: false,      // уведомление об ошибке при подтверждении "твердо"
            alertMessages: '',          // сообщение о выполнении запроса на подтверждение "твердо"
            alertMessagesLink: '',      // ссылка на подтвержденный запрос "твердо"
        }
    },

    mounted() {
        this.$store.dispatch('offer/GET_AUCTION_LIST');
    },

    computed: {
        /**
         * Список запросов на подтверждение "твердо" и с "твердо"
         * @return array
         */
        offers() {
            return this.$store.state.offer.auctionList;
        },
    },

    methods: {
        /**
         * Устанавливаем время статуса "твердо" для оффера с индексом idx
         * @param Object   e   событие
         * @param integer  idx id офферв в массиве offers
         */
        setTimeToOffer(e, idx) {
            let payload = {
                index : idx,
                val : e.target.selectedOptions[0].value,
            };
            this.$store.commit('offer/SET_AUCTION_LIST_TIME_BY_IDX', payload);
        },

        /**
         * Даем статус "твердо"
         * статус можно дать только одному запросу
         * @param  string  link ссылка на запрос "твердо"
         * @param  integer idx  номер запроса в массиве
         * @return
         */
        async startAuction(link, idx) {
            this.alertSuccessShow = false;
            this.alertErrorShow = false;

            var _param = new URLSearchParams();
            _param.append('link', link);
            _param.append('time', this.offers[idx].time);

            let res = await this.$axios.$post('/api/offer/create/auction', _param).then((res) => {
                return res;
            }).catch((error) => {
                return {
                    result: 'error',
                    messages: 'Ой! Возникла ошибка, попробуйте позже',
                };
            });

            this.alertMessages = res.messages;

            if (res.result == 'success') {
                this.alertMessagesLink = link;
                this.alertSuccessShow = true;
            } else {
                this.alertErrorShow = true;
            }

            window.scrollTo(0,0);
        },
    },
};
</script>



<style lang='scss'>
</style>
