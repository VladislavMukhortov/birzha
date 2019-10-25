// Страница с объявлениями которые подал пользователь

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="8">

                        <h1 class="section_title">My orders</h1>

                        <p>
                            <b-link to="/orders/archive">Archive</b-link>
                            <b-link to="/orders/complete">Complete</b-link>
                        </p>

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

                                <div class="small">Создано: {{ item.created_at }}</div>

                                <div>
                                    <template v-if="item.is_edit">
                                        <b-link class="btn btn-success" v-bind:to="'/orders/'+item.link">Редактировать</b-link>
                                    </template>

                                    <template v-if="item.is_remove">
                                        <b-button variant="danger" v-bind:id="'lot-item-remove-' + index">Удалить</b-button>
                                        <b-popover v-bind:target="'lot-item-remove-' + index" triggers="click blur" placement="top">
                                            <template v-slot:title>Подтвердить удаление</template>
                                            <b-button variant="danger" v-on:click="lotDeletByLink(item.link)">Удалить</b-button>
                                            <b-button v-on:click="onCloseLotPopover">Отмена</b-button>
                                        </b-popover>
                                    </template>

                                    <template v-if="item.is_auction">
                                        <b-link class="btn btn-primary" v-bind:to="'/deals/auction/'+item.offer_link">Оффер</b-link>
                                    </template>
                                </div>

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
            title: 'My orders | site.com',
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

        let res = await $axios.$get('/api/lot/list/my-orders', _param).then((res) => {
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

            let res = await this.$axios.$get('/api/lot/list/my-orders', _param).then((res) => {
                return res;
            }).catch((error) => {
                return { data:{}, pagination_page_count: 0};
            });

            this.lots = res.data;
            this.pagination_page_count = res.pagination_page_count;
        },

        /**
         * Удаление объявления
         * @param  string link ссылка на объявление
         * @return
         */
        async lotDeletByLink(link) {
            let _param = new URLSearchParams();
            _param.append('link', link);

            let res = await this.$axios.$post('/api/lot/delete/index', _param).then((res) => {
                return res;
            }).catch((error) => {
                return {
                    result: 'error',
                    messages: 'При удалении возникла ошибка, попробуйте позже',
                };
            });

            let variant = 'success';
            let content = 'Успешно удалено';

            if (res.result == 'success') {
                this.updateLotData();
            } else {
                variant = 'danger';
                content = res.messages;
            }

            this.$root.$emit('bv::hide::popover');

            this.$bvToast.toast(content, {
                title: 'Удаление объявления',
                autoHideDelay: 5000,
                variant: variant,
                solid: true,
                toaster: 'b-toaster-top-center',
            });
        },

        onCloseLotPopover() {
            this.$root.$emit('bv::hide::popover');
        },
    },

};
</script>



<style lang='scss'>
</style>
