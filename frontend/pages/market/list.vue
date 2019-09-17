<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="8" lg="8">

                        <h1 class="section_title text-center">{{ page_title }}</h1>

                        <b-pagination-nav
                            v-model="page_number"
                            v-bind:link-gen="linkGen"
                            v-bind:number-of-pages="pagination_page_count"
                            no-page-detect
                            use-router></b-pagination-nav>

                        <b-list-group>
                            <template v-for="ad in ads">
                                <b-list-group-item v-bind:to="'/market/'+ad.link">
                                    <pre>{{ ad }}</pre>
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
            title: 'Market crops | site.com',
            meta: [{ hid: 'description', name: 'description', content: '' }]
        }
    },

    data() {
        return {
            ads: {},                                    // объявления
            type: this.$route.query.type || 'all',      // тип объявлений (all, sell, buy)
            page_number: this.$route.query.page || 1,   // текущая страница
            pagination_page_count: 0,                   // кол-во страниц пагинации
            crop: {},                                   // информация о культуре
            page_title: '',                             // заголовок на странице
        }
    },

    /**
     * Получаем данные для начала работы
     * @param  {[type]} options.$axios [description]
     * @param  Object   options.route  параметры URL
     * @return Object                  информация о культуре, список объявлений, кол-во страниц с объявлениями
     */
    async asyncData({ $axios, route }) {
        let crop_param = {params: {
            crop_id: route.query.crop || 0
        }};

        let ads_param = {params: {
            crop_id: route.query.crop || 0,
            type_market: route.query.type || 'all',
            page: route.query.page || 1
        }};

        let [crop, ads] = await Promise.all([
            // информация о культуре
            $axios.$get('/api/crop/market-show/index', crop_param).then((res) => {
                return res;
            }),
            // список объявлений
            $axios.$get('/api/lot/market-list/index', ads_param).then((res) => {
                return res;
            })
        ]);

        return {
            crop: crop,
            ads: ads.data,
            pagination_page_count: ads.pagination_page_count,
        };
    },

    created() {
        // создаем заголовок на странице
        // в зависимости от параметра type в URL
        let crop_name = this.crop.name || '';
        switch (this.type) {
            case 'all':
                this.page_title = 'Market ' + crop_name;
                break;
            case 'sell':
                this.page_title = 'Selling ' + crop_name;
                break;
            case 'buy':
                this.page_title = 'Buying ' + crop_name;
                break;
            default:
                this.page_title = 'Market';
        }
    },

    watch: {
        /**
         * при переключении страниц в пагинации загружаем объввления
         */
        page_number: function() {
            this.updateAdsData();
        },
    },

    methods: {
        /**
         * Переключение пагинации через URL
         * @param  integer page_num номер страницы на которую переходим
         * @return string  url params
         */
        linkGen(page_num) {
            return `?crop=${this.crop.id}&page=${page_num}&type=${this.type}`;
        },

        /**
         * Запрос объявлений
         */
        async updateAdsData() {
            let ads_param = {params: {
                crop_id: this.crop.id,
                type_market: this.$route.query.type || 'all',
                page: this.page_number
            }};

            // список объявлений
            let ads = await this.$axios.$get('/api/lot/market-list/index', ads_param).then((res) => {
                return res;
            })

            this.ads = ads.data;
            this.pagination_page_count = ads.pagination_page_count;
        }
    },
};
</script>



<style lang='scss'>
</style>
