// Страница с объявлениями которые подал пользователь

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="8">

                        <h1 class="section_title">My orders</h1>

                        <b-pagination-nav
                            v-model="page_number"
                            v-bind:link-gen="linkGen"
                            v-bind:number-of-pages="pagination_page_count"
                            no-page-detect
                            use-router></b-pagination-nav>

                        <b-list-group>
                            <b-list-group-item v-for="item in lots">
                                <div class="order-item">
                                    <h4 class="order-item-title">
                                        <span>{{ item.title }}</span>
                                        <span class="order-item-price">{{ item.price }}</span>
                                    </h4>
                                    <div class="order-item-desc">
                                        <span>{{ item.deal }}</span>
                                        <span class="order-item-price">{{ item.quantity }} тонн</span>
                                    </div>
                                    <div>{{ item.quality }}</div>
                                    <div>{{ item.period }}</div>
                                    <div><b>{{ item.basis }}</b> | {{ item.basis_location }}</div>
                                    <div><b>Создано:</b> {{ item.created_at }}</div>
                                    <div class="text-danger">Статус: {{ item.status }}</div>
                                    <b-link v-bind:to="'/orders/edit/'+item.link">Редактировать</b-link>
                                </div>
                            </b-list-group-item>
                        </b-list-group>

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
            title: 'My orders | site.com',
            meta: [{ hid: 'description', name: 'description', content: '' }],
        }
    },

    data() {
        return {
            lots: {},                                 // сделки (офферы)
            page_number: this.$route.query.page || 1,   // текущая страница
            pagination_page_count: 0,                   // кол-во страниц с результатами
        }
    },

    /**
     * Получаем список объявлений
     * @param  {[type]} options.$axios
     * @param  {[type]} options.route
     * @return Object                  список объявлений, кол-во страниц с объявлениями
     */
    async asyncData({ $axios, route }) {
        let _param = {params: {
            page: route.query.page || 1
        }};

        const res = await $axios.$get('/api/lot/list/my-orders', _param).then((res) => {
            return res;
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

            /**
             * @param  Object res список объявлений
             */
            let res = await this.$axios.$get('/api/lot/list/my-orders', _param).then((res) => {
                return res;
            })

            this.lots = res.data;
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
