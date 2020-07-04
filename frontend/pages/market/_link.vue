// Страница с объявлением
// Можно посмотреть информацию об объявлении пока оно активно, то есть отображается на доске объявлений:
// - нет запросов "твердо"
// - есть запросы на "твердо"
// - есть "твердо"
//
// Можно:
// - запросить "твердо"
// - увидеть что запрос на "твердо" отправлен
// - увидеть что запрос на "твердо" подтвержден
//
// Не авторизированный пользователь ничего не увидит
// Пользователь смотрящий на свое объявление ничего не увидит

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row>

                    <b-col cols="12" md="8" lg="8">

                        <template v-if="$auth.loggedIn">

                            <template v-if="lot.offer == 'wait'">
                                <div class="wrap-link-page">
                                    <p>Вы уже подали заявку, ожидайте!</p>
                                    <p>
                                        <b-link
                                            to="/deals/auction"
                                            class="btn">Посмотреть список сделок</b-link>
                                    </p>
                                </div>
                            </template>

                            <template v-if="lot.offer == 'auction'">
                                <div class="wrap-link-page">
                                    <p>"твердо" до {{ lot.offer_ended_at }}</p>
                                    <p>
                                        <b-link
                                            v-bind:to="{name: 'deals-auction-link', params: {link: lot.offer_link}}"
                                            class="btn">Перейти на страницу оффера</b-link>
                                    </p>
                                </div>
                            </template>

                        </template>



                        <FullInfo v-bind:lot="lot" />



                        <template v-if="$auth.loggedIn">
                            <template v-if="lot.offer == 'free'">
                                <div class="wrap-bot-div">
                                    <p>Вы можете запросить статус "твердо". Владелец объявления увидит и подтвердит ваш запрос с указанием времени которое будет длиться "твердо".</p>

                                    <b-alert
                                        variant="success"
                                        fade
                                        v-bind:show="alertSuccess">
                                        <div>{{ alertText }}</div>
                                        <b-link
                                            to="/deals/auction"
                                            class="btn">Посмотреть список сделок</b-link>
                                    </b-alert>

                                    <b-alert
                                        variant="danger"
                                        fade
                                        v-bind:show="alertError">{{ alertText }}</b-alert>

                                    <b-button
                                        v-if="!alertSuccess"
                                        v-on:click="createOffer">Запросить "Твердо"</b-button>
                                </div>
                            </template>
                        </template>

                    </b-col>

                    <b-col cols="12" md="4" lg="4">
                        <div class="wrap-sidebar">
                            <p>
                                <b-link
                                    v-bind:to="{ path: '/market/list', query: { crop: lot.crop_id, page: 1, type: lot.deal }}"
                                    class="btn">Другие объявления</b-link>
                            </p>

                            <div>
                                <p>
                                    <b-link
                                    to="/orders/create"
                                    class="btn">Создать свое объявление</b-link>
                                </p>
                                <p>Только для зарегистрированных пользователей</p>
                            </div>
                        </div>
                    </b-col>

                </b-row>
            </b-container>
        </section>
    </main>

</template>



<script>
import FullInfo from '~/components/lot/FullInfo.vue';

export default {
    auth: false,

    components: {
        FullInfo,
    },

    head() {
        return {
            title: 'Market crops | site.com',
            meta: [
                { hid: 'description', name: 'description', content: '' }
            ],
        }
    },

    validate({ params }) {
        return /^[\w\-]+$/.test(params.link);
    },

    data() {
        return {
            lot: {},                // информация об объявлении
            alertSuccess: false,    // уведомление об успешной подаче "твердо"
            alertError: false,      // уведомление об ошибке при подаче "твердо"
            alertText: '',          // текст для уведомления создания запроса на "твердо"
        }
    },

    async asyncData({ $axios, params }) {
        let _param = {params: {
            link: params.link
        }};

        let data = await $axios.$get('/api/lot/show/market', _param).then((res) => {
            return res;
        }).catch((error) => {
            return { result: 'error', lot: {} };
        });

        // объявления нет, редиректим на 404
        if (data.result !== 'success') {
            $nuxt.$router.push('/market/404');
            return;
        }

        return { lot: data.lot };
    },

    methods: {
        /**
         * Посылаем запрос на "твердо"
         * создаем оффер со статусом ожидания "твердо"
         */
        async createOffer() {
            this.alertSuccess = false;
            this.alertError = false;

            var _param = new URLSearchParams();
            _param.append('link', this.$route.params.link);

            let res = await this.$axios.$post('/api/offer/create/request', _param).then((res) => {
                return res;
            }).catch((error) => {
                return {
                    result: 'error',
                    messages: 'Ой! Возникла ошибка, попробуйте позже',
                };
            });

            if (res.result == 'success') {
                this.alertSuccess = true;
            } else {
                this.alertError = true;
            }

            this.alertText = res.messages;
        },
    },

};
</script>



<style lang='scss'>
*{
    outline: none !important;
}
.wrap-link-page{
    border: 1px #d0c8d0 solid;
    margin-top: 5px;
    border-radius: 10px;
    padding: 10px;
    background: #f4f5f7;
}
div.wrap-link-page .btn{
    background: rgba(123,121,127, 1);
    border-radius: 10px;
    color: #000;
    border:none;
    text-align: center;
    transition: 0.3s;
}
div.wrap-link-page .btn:hover{
    background:  rgba(107,98,108, 0.6);
    border-color: rgba(107,98,108, 0.6);
}
.wrap-sidebar{
    margin-top: 5px;
    border: 1px #d0c8d0 solid;
    border-radius: 10px;
    padding: 10px;
    
}
.wrap-sidebar p .btn{
    background: rgba(123,121,127, 1);
    border-radius: 10px;
    color: #000;
    border:none;
    text-align: center;
    transition: 0.3s;
    width: 95%;
}
.wrap-sidebar p .btn:hover{
    background:  rgba(107,98,108, 0.6);
    border-color: rgba(107,98,108, 0.6);
}

.wrap-bot-div .btn{
    background: rgba(123,121,127, 1);
    border-radius: 10px;
    color: #000 !important;
    border:none;
    text-align: center;
    transition: 0.3s;
}

.wrap-bot-div .btn:hover{
    background:  rgba(107,98,108, 0.6);
    border-color: rgba(107,98,108, 0.6);
}

</style>
