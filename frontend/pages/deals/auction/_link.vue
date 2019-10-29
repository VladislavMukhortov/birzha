// Страница для сделки (оффера) который в статусе "твердо"
// ведется торг между сторонами сделки

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row>
                    <b-col cols="12" md="10" lg="8">

                        <h1 class="section_title">Auction deals order</h1>


                        <p>price: {{ lot.price }} {{ lot.currency }}</p>

                        <b-form-group label-class="required" label="цена:">
                            <b-input type="text" v-model="price"></b-input>
                        </b-form-group>

                        <b-button
                            v-if="lot.deal == 'sell'"
                            variant="secondary"
                            v-on:click="newPrice">Bid (понизить) цену</b-button>
                        <b-button
                            v-else
                            variant="secondary"
                            v-on:click="newPrice">Push (повысить) цену</b-button>



                        <pre>{{ lot }}</pre>
                        <pre>{{ offer }}</pre>

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
            title: 'Auction deals | site.com',
        }
    },

    validate({ params }) {
        return /^[\w\-]+$/.test(params.link)
    },

    data() {
        return {
            lot: {},
            offer: {},
            price: '',
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

        /**
         * @param  Object res информация об объявлении
         */
        let res = await $axios.$get('/api/lot/show/auction', _param).then((res) => {
            return res;
        });

        // // объявления нет, редиректим на 404
        // if (!res.success) {
        //     $nuxt.$router.push('/market/404');
        //     return;
        // }

        return {
            lot: res.offer,
            offer: res.lot,

        };
    },

    methods: {
        newPrice() {
            console.log('123');
        },
    },
};
</script>



<style lang='scss'>
</style>
