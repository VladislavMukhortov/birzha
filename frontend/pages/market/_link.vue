<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="10" lg="8">

                        <template v-if="lot.success == true">

                            <h1 class="section_title text-center">Lot</h1>
                            <pre>{{ lot }}</pre>

                            <p>
                                <span>Trader: </span>
                                <nuxt-link v-bind:to="'/trader/'+lot.trader_id">{{ lot.trader_name }}</nuxt-link>
                            </p>
                            <p>
                                <b-button
                                    variant="link"
                                    v-on:click="toBackPage">Back page</b-button>
                            </p>
                            <p>
                                <nuxt-link
                                    v-if="lot.deal == 'sell'"
                                    v-bind:to="{ path: '/market/seller', query: { crop: lot.crop_id, page: 1 }}">Other Seller {{ lot.crop }}</nuxt-link>
                                <nuxt-link
                                    v-else
                                    v-bind:to="{ path: '/market/buyer', query: { crop: lot.crop_id, page: 1 }}">Other Buyer {{ lot.crop }}</nuxt-link>
                            </p>

                        </template>
                        <template v-else>

                            <h1 class="section_title text-center">{{ lot.error }}</h1>
                            <p class="text-center">
                                <nuxt-link to="/market">TO Back</nuxt-link>
                            </p>

                        </template>

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
            title: 'Lot ' + this.lot.deal + ' '  + this.lot.crop + ' '  + this.lot.trader_name + ' ' + ' | site.com',
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
            lot: {
                success: false,
                error: '',
            },
        }
    },

    /**
     * получаем данные о объявлении
     * @return object
     */
    async asyncData ({ $axios, params }) {
        let lot_param = {params: {
            link: params.link
        }};

        let lot = await $axios.$get('/api/lot/market-show/index', lot_param).then((res) => {
            return res;
        });

        return { lot: lot };
    },

    methods: {
        /**
         * Редирект на страницу назад
         */
        toBackPage() {
            this.$router.back();
        }
    },

};
</script>



<style lang='scss'>

</style>
