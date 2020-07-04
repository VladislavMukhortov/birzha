// Страница с личными объявлениями которые подал пользователь
// которые находятся в архиве

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="8">

                        <h1 class="section_title">My archive orders</h1>
                        <div class="wrap-top-btn">
                            <p>
                                <span class="margin-for-320"><b-link class="btn" to="/orders">Active</b-link></span>
                                <b-link class="btn" to="/orders/archive">Archive</b-link>
                            </p>
                        </div>
                        <b-pagination-nav
                            v-if="lots.length"
                            v-model="page_number"
                            v-bind:link-gen="linkGen"
                            v-bind:number-of-pages="pagination_page_count"
                            no-page-detect
                            use-router></b-pagination-nav>

                        <b-list-group>
                            <b-list-group-item v-for="(item, index) in lots" v-bind:key="item.link">

                                <ShortDescriptionItemList v-bind:lot="item" />

                                <div class="text-muted">Создано: {{ item.created_at }}</div>

                                <b-link
                                    v-bind:to="{name: 'archive-link', params: {link: item.link}}"
                                    class="btn cust-btn">Посмотреть</b-link>

                            </b-list-group-item>
                        </b-list-group>

                        <b-pagination-nav
                            v-if="lots.length"
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
            title: 'My archive orders | site.com',
            meta: [{ hid: 'description', name: 'description', content: '' }],
        }
    },

    data() {
        return {
            lots: {},                                   // сделки (офферы)
            page_number: this.$route.query.page || 1,   // текущая страница
            pagination_page_count: 0,                   // кол-во страниц с результатами
        }
    },

    /**
     * Получаем список объявлений
     * @return Object   список объявлений, кол-во страниц с объявлениями
     */
    async asyncData({ $axios, route }) {
        let _param = {params: {
            page: route.query.page || 1
        }};

        let res = await $axios.$get('/api/lot/list/my-archive-orders', _param).then((res) => {
            return res;
        }).catch((error) => {
            return { data:{}, pagination_page_count: 0};
        });

        return {
            lots: res.data,
            pagination_page_count: res.pagination_page_count,
        };
    },

    watch: {
        /**
         * При изменении текущей страницы загружаем список объявлений для нее
         */
        page_number: function() {
            this.updateLotData();
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
         * Загрузка списка объявлений
         */
        async updateLotData() {
            let _param = {params: {
                page: this.page_number
            }};

            let res = await this.$axios.$get('/api/lot/list/my-active-orders', _param).then((res) => {
                return res;
            }).catch((error) => {
                return { data:{}, pagination_page_count: 0};
            });

            this.lots = res.data;
            this.pagination_page_count = res.pagination_page_count;
        },
    },

};
</script>



<style lang='scss'>
*{
    margin: 0 auto;
}
</style>
