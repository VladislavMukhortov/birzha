// Страница с объявлением
// Можно посмотреть информацию об объявлении пока оно активно, то есть отображается на доске объявлений:
// - нет запросов оффера
// - оффер(ы) на стадии подтверждения
// - оффер со статусом "твердо"

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="10" lg="8">

                        <h1 class="section_title text-center">Lot</h1>

                        <template v-if="lot.offer == 'free'">
                            <p>Вы можете запросить статус "твердо". Когда владелец объявления увидит ваш запрос, то на его усмотрение он укажет время которое будет длиться статус.</p>
                            <b-alert
                                v-bind:variant="alertCreateOfferVariant"
                                dismissible
                                fade
                                v-bind:show="alertCreateOfferShow"
                                v-on:dismissed="alertCreateOfferShow=false">
                                <div>{{ alertCreateOfferText }}</div>
                                <b-link to="/deals/auction">Посмотреть список сделок</b-link>
                            </b-alert>

                            <b-button variant="primary" v-on:click="createOffer">Запросить "Твердо"</b-button>
                        </template>
                        <template v-else-if="lot.offer == 'wait'">
                            <p>Вы уже подали заявку, ожидайте!</p>
                            <p>
                                <b-link to="/deals/auction">Посмотреть список сделок</b-link>
                            </p>
                        </template>
                        <template v-else-if="lot.offer == 'auction'">
                            <p>Идет статус "твердо" до {{ lot.offer_ended_at }}</p>
                            <p>
                                <b-link v-bind:to="'/deals/auction/'+lot.offer_link">Перейти на страницу оффера</b-link>
                            </p>
                        </template>

                        <hr>

                        <p>
                            <b-link v-bind:to="{ path: '/market/list', query: { crop: lot.crop_id, page: 1, type: lot.deal }}">Back to market</b-link>
                        </p>

                        <hr>

                        <pre>{{ lot }}</pre>

                    </b-col>
                </b-row>
            </b-container>
        </section>
    </main>

</template>



<script>
export default {
    auth: false,

    head() {
        return {
            title: this.lot.title + ' | site.com',
            meta: [
                { hid: 'description', name: 'description', content: '' }
            ]
        }
    },

    validate({ params }) {
        return /^[\w\-]+$/.test(params.link)
    },

    /**
     * Параметры в объекте, что бы избежать ошибок при 404
     */
    data() {
        return {
            lot: {},                                // информация об объявлении

            alertCreateOfferText: '',           // некорректный логин или пароль
            alertCreateOfferVariant: 'success', // некорректный логин или пароль
            alertCreateOfferShow: false,        // некорректный логин или пароль
        }
    },

    /**
     * получаем данные о объявлении
     * @return object
     */
    async asyncData({ $axios, params }) {
        let lot_param = {params: {
            link: params.link
        }};

        /**
         * @param  Object res информация об объявлении
         */
        let res = await $axios.$get('/api/lot/show/market', lot_param).then((res) => {
            return res;
        });

        // объявления нет, редиректим на 404
        if (!res.success) {
            $nuxt.$router.push('/market/404');
            return;
        }

        return { lot: res.lot };
    },

    methods: {
        /**
         * Создаем оффер со статусом ожидания "твердо"
         */
        async createOffer() {
            var params = new URLSearchParams();
            params.append('link', this.lot.link);

            let res = await this.$axios.$post('/api/offer/create/index', params).then((res) => {
                return res;
            });

            this.alertCreateOfferVariant = (res.success) ? 'success' : 'danger';
            this.alertCreateOfferText = (res.text) ? res.text : '';
            this.alertCreateOfferShow = true;
        }
    },
};
</script>



<style lang='scss'>
</style>
