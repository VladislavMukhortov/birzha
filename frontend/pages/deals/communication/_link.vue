// Страница для сделки (оффера) который на этапе общения
// не показывать контакты друг друга

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row>

                    <b-col cols="12" md="8" lg="8">


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

export default {
    components: {
        FullInfo,
    },

    head() {
        return {
            title: 'Communication deals | site.com',
        }
    },

    validate({ params }) {
        return /^[\w\-]+$/.test(params.link)
    },

    data() {
        return {
            lot: {},
            // offer: {},
        }
    },

    async asyncData({ $axios, params }) {
        let _param = {params: {
            link: params.link
        }};

        let data = await $axios.$get('/api/lot/show/communication', _param).then((res) => {
            return res;
        }).catch((error) => {
            return { result: 'error', lot: {} };
        });

        // объявления нет
        if (data.result !== 'success') {
            $nuxt.$router.push('/deals/communication');
            return;
        }

        return {
            lot: data.lot,
            // offer: data.offer,
        };
    },

    methods: {

    },
};
</script>



<style lang='scss'>
</style>
