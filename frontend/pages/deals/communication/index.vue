// Страница с офферами которые находятся на стадии общения

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="8" lg="8">

                        <h1 class="section_title">Communication deals</h1>

                        <b-pagination-nav
                            v-if="offers.length"
                            v-model="page_number"
                            v-bind:link-gen="linkGen"
                            v-bind:number-of-pages="pagination_page_count"
                            no-page-detect
                            use-router></b-pagination-nav>

                        <b-list-group v-if="offers.length">
                            <b-list-group-item v-for="item in offers" v-bind:key="item.link">

                                <ShortDescriptionItemList v-bind:lot="item" />

                                <div class="custom-btn">
                                    <b-link
                                        v-bind:to="{name: 'deals-communication-link', params: {link: item.link}}"
                                        class="btn">Перейти в сделку</b-link>
                                </div>

                            </b-list-group-item>
                        </b-list-group>
                        <div v-else class="custom-btn">
                            <h2 class="section_subtitle">У вас нет сделок на этапе общения</h2>
                            <b-link to="/deals" class="btn">Deals</b-link>
                        </div>

                        <b-pagination-nav
                            v-if="offers.length"
                            v-model="page_number"
                            v-bind:link-gen="linkGen"
                            v-bind:number-of-pages="pagination_page_count"
                            no-page-detect
                            use-router></b-pagination-nav>

                    </b-col>
                </b-row>
            </b-container>
        </section>
    </main>

</template>



<script>
import ShortDescriptionItemList from '~/components/lot/ShortDescriptionItemList.vue';

export default {
    components: {
        ShortDescriptionItemList,
    },

    head() {
        return {
            title: 'Communication deals | site.com',
            meta: [{ hid: 'description', name: 'description', content: '' }]
        }
    },

    data() {
        return {
            offers: {},                                 // сделки (офферы)
            page_number: this.$route.query.page || 1,   // текущая страница
            pagination_page_count: 0,                   // кол-во страниц с результатами
        }
    },

    /**
     * Получаем данные для начала работы
     * @param  {[type]} options.$axios [description]
     * @param  Object   options.route  параметры URL
     * @return Object                  список сделок (офферов), кол-во страниц с объявлениями
     */
    async asyncData({ $axios, route }) {
        let _param = {params: {
            page: route.query.page || 1
        }};

        /**
         * @param  Object res список сделок
         */
        let res = await $axios.$get('/api/offer/list/communication', _param).then((res) => {
            return res;
        }).catch((error) => {
            return { data: [], pagination_page_count: 0 };
        });

        return {
            offers: res.data,
            pagination_page_count: res.pagination_page_count,
        };
    },

    watch: {
        /**
         * При изменении текущей страницы загружаем список сделок для нее
         */
        page_number: function() {
            this.updateOffersData();
        },
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
         * Загрузка списка сделок
         */
        async updateOffersData() {
            let _param = {params: {
                page: this.page_number
            }};

            /**
             * @param  Object res список сделок
             */
            let res = await this.$axios.$get('/api/offer/list/communication', _param).then((res) => {
                return res;
            }).catch((error) => {
                return { data: [], pagination_page_count: 0 };
            });

            this.offers = res.data;
            this.pagination_page_count = res.pagination_page_count;
        },
    },
};
</script>



<style lang='scss'>
@media (max-width: 768px){
    .custom-btn a{
        width: 100%;
        display: inline-block;
        background: rgba(123,121,127, 1) !important;
        border-radius: 10px !important;
        border: 1px #000 solid !important;
        color: #000 !important;
        text-align: center;
        transition: 0.3s;
    }
    .custom-btn a:hover{
        background:  rgba(107,98,108, 0.6) !important;
        border-color: rgba(107,98,108, 0.6) !important;
    }
}
@media (min-width: 1024px){
    .custom-btn a{
        width: 45%;
        display: inline-block;
        background: rgba(123,121,127, 1) !important;
        border-radius: 10px !important;
        border: 1px #000 solid !important;
        color: #000 !important;
        text-align: center;
        transition: 0.3s;
    }
    .custom-btn a:hover{
        background:  rgba(107,98,108, 0.6) !important;
        border-color: rgba(107,98,108, 0.6) !important;
    }
}

</style>
