// Страница с сделками (офферы) к объявлениям:
// - запрос на "твердо" которое запросил я - даем ссылку на лот с доски
// - запрос на "твердо" которое запросили к моему объявлению - даем ссылку на лот в моих объявлениях
// - статус "твердо" - ссылка на объявление для торга

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="8" lg="8">

                        <h1 class="section_title">Auction deals</h1>

                        <b-pagination-nav
                            v-if="offers.length"
                            v-model="page_number"
                            v-bind:link-gen="linkGen"
                            v-bind:number-of-pages="pagination_page_count"
                            no-page-detect
                            use-router></b-pagination-nav>

                        <b-list-group>
                            <b-list-group-item v-for="item in offers" v-bind:key="item.link">

                                <ShortDescriptionItemList v-bind:lot="item" />

                                <div class="text-danger">{{ item.desc }}</div>

                                <div>
                                    <template v-if="item.is_auction">
                                        <b-link class="btn btn-primary" v-bind:to="'/deals/auction/'+item.link">Перейти в сделку</b-link>
                                    </template>
                                    <template v-else>
                                        <template v-if="item.is_my">
                                            <b-link class="btn btn-info" v-bind:to="'/orders/'+item.link">Посмотреть запросы к объявлению</b-link>
                                        </template>
                                        <template v-else>
                                            <b-link class="btn btn-secondary" v-bind:to="'/market/'+item.link">Посмотреть объявление</b-link>
                                        </template>
                                    </template>
                                </div>

                            </b-list-group-item>
                        </b-list-group>

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
            title: 'Auction deals | site.com',
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
        let res = await $axios.$get('/api/offer/list/auction', _param).then((res) => {
            return res;
        })

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
            let res = await this.$axios.$get('/api/offer/list/auction', _param).then((res) => {
                return res;
            })

            this.offers = res.data;
            this.pagination_page_count = res.pagination_page_count;
        },
    },
};
</script>



<style lang='scss'>
.order-item {
}

.order-item-title {
    position: relative;
    padding: 0 150px 0 0;
    text-transform: uppercase;
}

.order-item-price {
    position: absolute;
    top: 0;
    right: 0;
}

.order-item-desc {
    position: relative;
    padding: 0 150px 0 0;
}
</style>
