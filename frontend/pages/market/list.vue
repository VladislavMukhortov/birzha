// Страница с объявлениями для выбранной культуры и типа (покупка/продажа)

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="8" lg="8">
                        <h1 class="section_title text-center">{{ page_title }}</h1>

                        <b-pagination-nav
                            v-if="ads.length"
                            v-model="page_number"
                            v-bind:link-gen="linkGen"
                            v-bind:number-of-pages="pagination_page_count"
                            no-page-detect
                            use-router>
                        </b-pagination-nav>
                        <span class="wrap-list">
                            <b-list-group>
                                <b-list-group-item
                                    v-for="item in ads"
                                    v-bind:to="{name: 'market-link', params: {link: item.link}}"
                                    v-bind:key="item.link">

                                    <ShortDescriptionItemList v-bind:lot="item" />
                                    <div class="more-info">
                                        <span class="btn-link">Подробнее</span>
                                    </div>

                                </b-list-group-item>
                            </b-list-group>
                        </span>

                        <b-pagination-nav
                            v-if="ads.length"
                            v-model="page_number"
                            v-bind:link-gen="linkGen"
                            v-bind:number-of-pages="pagination_page_count"
                            no-page-detect
                            use-router>
                        </b-pagination-nav>

                    </b-col>
                </b-row>
            </b-container>
        </section>
    </main>

</template>



<script>
import ShortDescriptionItemList from '~/components/lot/ShortDescriptionItemList.vue';

export default {
    auth: false,

    components: {
        ShortDescriptionItemList,
    },

    head() {
        return {
            title: 'Market crops | site.com',
            meta: [{ hid: 'description', name: 'description', content: '' }],
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
            }).catch((error) => {
                return {};
            }),
            $axios.$get('/api/lot/list/market', ads_param).then((res) => {
                return res;
            }).catch((error) => {
                return { data:{}, pagination_page_count: 0};
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
             * @param  Object res список объявлений
             */
            let res = await this.$axios.$get('/api/lot/list/market', ads_param).then((res) => {
                return res;
            }).catch((error) => {
                return { data:{}, pagination_page_count: 0};
            });

            this.ads = res.data;
            this.pagination_page_count = res.pagination_page_count;
        },
    },
};
</script>



<style lang='scss'>

.more-info{
    background: rgba(123,121,127, 1);
    border-radius: 10px;
    width: 200px;
    height: 30px; 
    text-align: center;
    transition: 0.3s;
}
.more-info:hover{
    background:  rgba(107,98,108, 0.6);
}
.btn-link{
    color: #000;
    font-size: 20px;
}
.btn-link:hover{
    text-decoration: none;
    color: #000;
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
