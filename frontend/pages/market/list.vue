// Страница с объявлениями для выбранной культуры и типа (покупка/продажа)

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
                                <b-list-group-item
                                    v-bind:to="'/market/'+ad.link"
                                    v-bind:class="{ 'bg-warning': !ad.status }">
                                    <div class="order-item">
                                        <h4 class="order-item-title">
                                            <span>{{ ad.title }}</span>
                                            <span class="order-item-price">{{ ad.price }}</span>
                                        </h4>
                                        <div class="order-item-desc">
                                            <span>{{ ad.deal }}</span>
                                            <span class="order-item-price">{{ ad.quantity }} тонн</span>
                                        </div>
                                        <div>{{ ad.quality }}</div>
                                        <div>{{ ad.period }}</div>
                                        <div><b>{{ ad.basis }}</b> | {{ ad.basis_location }}</div>
                                    </div>
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
            pagination_page_count: 0,                   // кол-во страниц с результатами
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

        /**
         * @param  Object crop информация о культуре которую торгуют на этой странице
         * @param  Object ads  список объявлений
         */
        let [crop, ads] = await Promise.all([
            $axios.$get('/api/crop/show/market', crop_param).then((res) => {
                return res;
            }),
            $axios.$get('/api/lot/list/market', ads_param).then((res) => {
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
         * При изменении текущей страницы загружаем список объявлений для нее
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
         * Загрузка списка объявлений
         */
        async updateAdsData() {
            let ads_param = {params: {
                crop_id: this.crop.id,
                type_market: this.$route.query.type || 'all',
                page: this.page_number
            }};

            /**
             * @param  Object ads список объявлений
             */
            let ads = await this.$axios.$get('/api/lot/list/market', ads_param).then((res) => {
                return res;
            })

            this.ads = ads.data;
            this.pagination_page_count = ads.pagination_page_count;
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
