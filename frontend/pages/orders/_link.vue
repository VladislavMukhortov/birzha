// Страница со своим объявлением

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="10" lg="8">

                        <h1 class="section_title text-center">My orders</h1>

                        <pre>{{ lot }}</pre>

                        <b-list-group>
                            <b-list-group-item v-for="item in offers" v-bind:key="item.link">
                                <span>#{{ item.id }}</span>
                                <span>Запросили: {{ item.created_at }}</span>
                                <span>{{ item.desc }}</span>
                                <template v-if="item.status">
                                    <b-link to="">Перейти</b-link>
                                </template>
                                <template v-else>
                                    <b-button variant="primary" v-on:click="startAuction(item.link)">Дать твердо</b-button>
                                </template>
                            </b-list-group-item>
                        </b-list-group>

                        <p>
                            <nuxt-link to="/orders">Back to my orders</nuxt-link>
                        </p>

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
            title: this.lot.title + ' | site.com',
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
            lot: {},    // информация об объявлении
            offers: {},    // информация об объявлении
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
        let res = await $axios.$get('/api/lot/show/my-orders', lot_param).then((res) => {
            return res;
        });

        // объявления нет, редиректим на 404
        if (res.result == 'error') {
            $nuxt.$router.push('/orders/404');
            return;
        }

        return {
            lot: res.data,
            offers: res.offers,
        };
    },

    methods: {
        startAuction(link) {
            console.log(link);
        }
    },
};
</script>



<style lang='scss'>
</style>
