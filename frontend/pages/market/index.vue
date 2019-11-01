// Страница со списком культур для каждого направления:
// - покука
// - продажа
// - все вместе

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="8" lg="8">

                        <h1 class="section_title text-center">Market</h1>

                        <b-tabs content-class="market-content" justified pills align="center">
                            <b-tab title="All" active>
                                <b-list-group>
                                    <template v-for="crop in crops">
                                        <b-list-group-item v-bind:to="{ path: '/market/list', query: { crop: crop.id, page: 1, type: 'all' }}">{{ crop.name }}</b-list-group-item>
                                    </template>
                                </b-list-group>
                            </b-tab>

                            <b-tab title="Seller">
                                <b-list-group>
                                    <template v-for="crop in crops">
                                        <b-list-group-item v-bind:to="{ path: '/market/list', query: { crop: crop.id, page: 1, type: 'sell' }}">{{ crop.name }}</b-list-group-item>
                                    </template>
                                </b-list-group>
                            </b-tab>

                            <b-tab title="Buyer">
                                <b-list-group>
                                    <template v-for="crop in crops">
                                        <b-list-group-item v-bind:to="{ path: '/market/list', query: { crop: crop.id, page: 1, type: 'buy' }}">{{ crop.name }}</b-list-group-item>
                                    </template>
                                </b-list-group>
                            </b-tab>
                        </b-tabs>

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
            title: 'Market crops | site.com',
            meta: [{ hid: 'description', name: 'description', content: '' }],
        }
    },

    data() {
        return {
            crops: {}
        }
    },

    async asyncData ({ $axios }) {
        let res = await $axios.$get('/api/crop/list/market').then((res) => {
            return res;
        }).catch((error) => {
            return {};
        });

        return { crops: res };
    }
};
</script>



<style lang='scss'>
.market-content {
    margin: 1rem 0 0 0;
}
</style>
