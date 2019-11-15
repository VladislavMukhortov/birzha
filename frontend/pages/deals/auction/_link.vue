// Страница для сделки (оффера) который в статусе "твердо"
// ведется торг между сторонами сделки

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row>

                    <b-col cols="12" md="8" lg="8">

                        <BargainingUsersBlock v-bind:offer="offer" />

                        <hr>

                        <FullInfo v-bind:lot="lot" />

                    </b-col>

                </b-row>
            </b-container>
        </section>
    </main>

</template>



<script>
import FullInfo from '~/components/lot/FullInfo.vue';
import BargainingUsersBlock from '~/components/offer/BargainingUsersBlock.vue';

export default {
    components: {
        FullInfo,
        BargainingUsersBlock,
    },

    head() {
        return {
            title: 'Auction deals | site.com',
        }
    },

    validate({ params }) {
        return /^[\w\-]+$/.test(params.link);
    },

    data() {
        return {
            lot: {},
            offer: {},
            price: '',
        }
    },

    async asyncData({ $axios, params }) {
        let _param = {params: {
            link: params.link
        }};

        let data = await $axios.$get('/api/lot/show/auction', _param).then((res) => {
            return res;
        }).catch((error) => {
            return { result: 'error', lot: {} };
        });

        // объявления нет
        if (data.result !== 'success') {
            $nuxt.$router.push('/deals/auction');
            return;
        }

        return {
            lot: data.lot,
            offer: data.offer,
        };
    },
};
</script>



<style lang='scss'>
</style>
