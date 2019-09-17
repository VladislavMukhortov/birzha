<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="10" lg="8">

                        <h1 class="section_title text-center">Trader:</h1>

                        <pre>{{ trader }}</pre>

                        <p>
                            <b-button
                                variant="link"
                                v-on:click="toBackPage">Back page</b-button>
                        </p>

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
            title: 'Trader #' + this.trader.id + ' | site.com',
            meta: [
                { hid: 'description', name: 'description', content: '' }
            ]
        }
    },

    validate({ params }) {
        return /^\d+$/.test(params.id)
    },

    data() {
        return {
            trader: {},
        }
    },

    async asyncData ({ $axios, params }) {
        let trader_param = {params: {
            id: params.id
        }};

        let trader = await $axios.$get('/api/company/show/index', trader_param).then((res) => {
            return res;
        });

        return { trader: trader };
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
