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
                                        <b-list-group-item v-bind:to="{ path: '/market/list', query: { crop: crop.id, page: 1, type: 'all' }}">
                                            <span>{{ crop.name }}</span>
                                        </b-list-group-item>
                                    </template>
                                </b-list-group>
                            </b-tab>

                            <b-tab title="Seller">
                                <b-list-group>
                                    <template v-for="crop in crops">
                                        <b-list-group-item v-bind:to="{ path: '/market/list', query: { crop: crop.id, page: 1, type: 'sell' }}">
                                            <span>{{ crop.name }}</span>
                                        </b-list-group-item>
                                    </template>
                                </b-list-group>
                            </b-tab>

                            <b-tab title="Buyer">
                                <b-list-group>
                                    <template v-for="crop in crops">
                                        <b-list-group-item v-bind:to="{ path: '/market/list', query: { crop: crop.id, page: 1, type: 'buy' }}">
                                            <span>{{ crop.name }}</span>
                                        </b-list-group-item>
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
            title: 'Market | site.com',
            meta: [{ hid: 'description', name: 'description', content: '' }]
        }
    },

    data() {
        return {
            crops: {}
        }
    },

    async asyncData ({ $axios }) {
        const crops = await $axios.$get('/api/crop/list/market').then((res) => {
            return res;
        });

        return { crops: crops };
    }
};
</script>



<style lang='scss'>
.market-content {
    margin: 1rem 0 0 0;
}
</style>
