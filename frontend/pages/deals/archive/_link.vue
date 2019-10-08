// Страница для сделки (оффера) который в статусе "твердо"
// ведется торг между сторонами сделки

<template>

    <main>

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
            offer: {},
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
        // let res = await $axios.$get('/api/lot/market-show/index', lot_param).then((res) => {
        //     return res;
        // });

        // // объявления нет, редиректим на 404
        // if (!res.success) {
        //     $nuxt.$router.push('/market/404');
        //     return;
        // }

        // return { lot: res.lot };
        return true;
    },

    methods: {

    },
};
</script>



<style lang='scss'>
</style>
