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
                                <p>Вы уже подали заявку, ожидайте!</p>
                                <p>
                                    <b-link
                                        to="/deals/auction"
                                        class="btn btn-primary">Посмотреть список сделок</b-link>
                                </p>
                            </template>

                            <template v-if="lot.offer == 'auction'">
                                <p>"твердо" до {{ lot.offer_ended_at }}</p>
                                <p>
                                    <b-link
                                        v-bind:to="{name: 'deals-auction-link', params: {link: lot.offer_link}}"
                                        class="btn btn-primary">Перейти на страницу оффера</b-link>
                                </p>
                            </template>

                        </template>



                        <FullInfo v-bind:lot="lot" />



                        <template v-if="$auth.loggedIn">
                            <template v-if="lot.offer == 'free'">
                                <p>Вы можете запросить статус "твердо". Владелец объявления увидит и подтвердит ваш запрос с указанием времени которое будет длиться "твердо".</p>

                                <b-alert
                                    variant="success"
                                    fade
                                    v-bind:show="alertSuccess">
                                    <div>{{ alertText }}</div>
                                    <b-link
                                        to="/deals/auction"
                                        class="btn btn-primary">Посмотреть список сделок</b-link>
                                </b-alert>

                                <b-alert
                                    variant="danger"
                                    fade
                                    v-bind:show="alertError">{{ alertText }}</b-alert>

                                <b-button
                                    v-if="!alertSuccess"
                                    variant="primary"
                                    v-on:click="createOffer">Запросить "Твердо"</b-button>
                            </template>
                        </template>

                    </b-col>

                    <b-col cols="12" md="4" lg="4">
                        <p>
                            <b-link
                                v-bind:to="{ path: '/market/list', query: { crop: lot.crop_id, page: 1, type: lot.deal }}"
                                class="btn btn-secondary">Другие объявления</b-link>
                        </p>

                        <div>
                            <p>
                                <b-link
                                to="/orders/create"
                                class="btn btn-success">Создать свое объявление</b-link>
                            </p>
                            <p>Только для зарегистрированных пользователей</p>
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
</style>
