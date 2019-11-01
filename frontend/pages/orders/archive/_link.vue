// Страница со своим объявлением, которые в архиве

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row>
                    <b-col cols="12" md="10" lg="8">

                        <h1 class="section_title">My orders</h1>

                        <pre>{{ lot }}</pre>

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
            title: 'Edit order | site.com',
        }
    },

    validate({ params }) {
        return /^[\w\-]+$/.test(params.link)
    },

    data() {
        return {
            lot: {},     // нельзя редактировать объявление
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

        let lot = await $axios.$get('/api/lot/show/my-order', _param).then((res) => {
            return res;
        });

        // объявления нет, редиректим на 404
        if (lot.result == 'error') {
            $nuxt.$router.push('/orders/404');
            return;
        }

        return {
            lot: lot.data,
        };
    },

};
</script>



<style lang='scss'>
</style>
