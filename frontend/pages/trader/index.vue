<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="8" lg="6">

                        <h1 class="section_title text-center">Traders</h1>

                        <b-pagination-nav
                            v-model="page_number"
                            v-bind:link-gen="linkGen"
                            v-bind:number-of-pages="pagination_page_count"
                            no-page-detect
                            use-router></b-pagination-nav>

                        <b-list-group>
                            <template v-for="trader in traders">
                                <b-list-group-item v-bind:to="'/trader/'+trader.id">
                                    <pre>{{ trader }}</pre>
                                </b-list-group-item>
                            </template>
                        </b-list-group>

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
            title: 'Traders',
            meta: [
                { hid: 'description', name: 'description', content: '' }
            ]
        }
    },

    data() {
        return {
            traders: {},                                // трейдеры
            page_number: this.$route.query.page || 1,   // текущая страница
            pagination_page_count: 0,                   // кол-во страниц пагинации
        }
    },

    async asyncData ({ $axios, route }) {
        let traders_param = {params: {
            page: route.query.page || 1
        }};

        let traders = await $axios.$get('/api/company/list/index', traders_param).then((res) => {
            return res;
        });

        return {
            traders: traders.data,
            pagination_page_count: traders.pagination_page_count
        };
    },

    watch: {
        page_number: function() {
            this.updateAdsData();
        }
    },

    methods: {
        /**
         * Переключение пагинации через URL
         * @param  integer page_num номер страницы на которую переходим
         * @return string  url params
         */
        linkGen(page_num) {
            return `?page=${page_num}`;
        },

        /**
         * обновляем данные трейдеров
         * @return void
         */
        async updateAdsData() {
            let traders_param = {params: {
                page: this.page_number
            }};

            let traders = await this.$axios.$get('/api/trader/list/index', traders_param).then((res) => {
                return res;
            })

            this.traders = traders.data;
            this.pagination_page_count = traders.pagination_page_count;
        },
    },
};
</script>



<style lang='scss'>

</style>
