// Страница с личными объявлениями которые подал пользователь
// которые отображаются на доске
// - не имеют запросов на "твердо"
// - имеют запросы на "твердо"
// - имеют "твердо"

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="8">

                        <h1 class="section_title section_title-align">My orders</h1>
                        <div class="wrap-top-btn for-margin">
                            
                                <span class="margin-for-320"><b-link class="btn" to="/orders">Active</b-link></span>
                                <b-link class="btn" to="/orders/archive">Archive</b-link>
                            
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

                                <template v-if="item.waiting_offer_count && !item.is_auction">
                                    <div class="text-danger">Запросов "твердо": {{ item.waiting_offer_count }}</div>
                                </template>

                                <div>
                                    <div class="item-btn">
                                        <b-link
                                            v-bind:to="{name: 'orders-link', params: {link: item.link}}"
                                            class="btn cust-btn">Посмотреть</b-link>

                                        <template v-if="item.is_edit">
                                            <b-link
                                                v-bind:to="{name: 'orders-link', params: {link: item.link}}"
                                                class="btn cust-btn">Редактировать</b-link>
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
                                            <b-link
                                                v-bind:to="{name: 'deals-auction-link', params: {link: item.offer_link}}"
                                                class="btn btn-primary">Оффер</b-link>
                                        </template>
                                    </div>
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

        let res = await $axios.$get('/api/lot/list/my-active-orders', _param).then((res) => {
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

            // закрываем все popover
            this.$root.$emit('bv::hide::popover');

            // показываем уведомление что объявление удалено
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
.section_title-align{
    text-align: center;
}
.for-margin{
    margin-bottom: 16px;
}
@media(min-width: 320px){
    .wrap-top-btn{
        display: inline-block;
        width: 100%;
    }
    .wrap-top-btn a.btn{
        // margin-bottom: 31px;
        width: 100%;
        display: inline-block;
        border-radius: 10px;
        color: #000;
        border: 1px #000 solid;
        text-align: center;
        transition: 0.3s;
    }
    .wrap-top-btn a.nuxt-link-exact-active{
        // margin-bottom: 31px;
        width: 100%;
        display: inline-block;
        background: rgba(123,121,127, 1);
        border-radius: 10px;
        border: 1px #000 solid;
        color: #000;
        text-align: center;
        transition: 0.3s;

    }
    .margin-for-320 a{
        margin-bottom: 10px;
    }
    .cust-btn{
        margin-bottom: 15px;
        width: 100%;
        display: inline-block;
        background: rgba(123,121,127, 1);
        border-radius: 10px;
        border: 1px #000 solid;
        color: #000;
        text-align: center;
        transition: 0.3s;
    }
    .item-btn .btn-danger{
        margin-bottom: 15px;
        width: 100%;
        display: inline-block;
        border-radius: 10px;
        color: #000 !important;
        text-align: center;
        transition: 0.3s;
    }
}
@media(min-width: 1024px){
    .wrap-top-btn{
        display: inline-block;
        width: 100%;
    }
    .wrap-top-btn a.btn{
        // margin-bottom: 31px;
        width: 49.6%;
        display: inline-block;
        border-radius: 10px;
        color: #000;
        border: 1px #000 solid;
        text-align: center;
        transition: 0.3s;
    }
    .wrap-top-btn a.nuxt-link-exact-active{
        // margin-bottom: 31px;
        width: 49.6%;
        display: inline-block;
        background: rgba(123,121,127, 1);
        border-radius: 10px;
        border: 1px #000 solid;
        color: #000;
        text-align: center;
        transition: 0.3s;
    }
    .margin-for-320 a{
        margin-bottom: 0px;
    }
    .cust-btn{
        margin-bottom: 15px;
        width: 38%;
        display: inline-block;
        background: rgba(123,121,127, 1);
        border-radius: 10px;
        border: 1px #000 solid;
        color: #000;
        text-align: center;
        transition: 0.3s;
    }
    .item-btn .btn-danger{
        margin-bottom: 15px;
        width: 22.5%;
        display: inline-block;
        border-radius: 10px;
        color: #000 !important;
        text-align: center;
        transition: 0.3s;
    }
}
@media(min-width: 1400px){
    .wrap-top-btn{
        display: inline-block;
        width: 100%;
    }
    .wrap-top-btn a.btn{
        // margin-bottom: 31px;
        width: 49.7%;
        display: inline-block;
        border-radius: 10px;
        color: #000;
        border: 1px #000 solid;
        text-align: center;
        transition: 0.3s;
    }
    .wrap-top-btn a.nuxt-link-exact-active{
        // margin-bottom: 31px;
        width: 49.7%;
        display: inline-block;
        background: rgba(123,121,127, 1);
        border-radius: 10px;
        border: 1px #000 solid;
        color: #000;
        text-align: center;
        transition: 0.3s;
    }
    .margin-for-320 a{
        margin-bottom: 0px;
    }
    .cust-btn{
        margin-bottom: 15px;
        width: 38%;
        display: inline-block;
        background: rgba(123,121,127, 1);
        border-radius: 10px;
        border: 1px #000 solid;
        color: #000;
        text-align: center;
        transition: 0.3s;
    }
    .item-btn .btn-danger{
        margin-bottom: 15px;
        width: 22.5%;
        display: inline-block;
        border-radius: 10px;
        color: #000 !important;
        text-align: center;
        transition: 0.3s;
    }
}

.wrap-top-btn a.nuxt-link-exact-active:hover{
    background:  rgba(107,98,108, 0.6);
    border-color: rgba(107,98,108, 0.6);
}
.btn:hover{
    background:  rgba(107,98,108, 0.6);
    border-color: rgba(107,98,108, 0.6);
}
.item-btn{
    width: 100%;  
}
.cust-btn:hover{
    background:  rgba(107,98,108, 0.6);
    border-color: rgba(107,98,108, 0.6);
}
.item-btn .btn-danger:hover{
    background:  rgba(247,112,137, 0.6);
    border-color: rgba(247,112,137, 0.6);
}
.popover{
    width: 100%;
}
.popover .btn-danger{
    margin-bottom: 15px !important;
    width: 49% !important;
    display: inline-block !important;
    border-radius: 10px !important;
    background: #ff2d55 !important;
    border: 1px #ff2d55 solid !important;
    color: #000 !important;
    text-align: center !important;
    transition: 0.3s !important;
}
.popover .btn-danger:hover{
    background:  rgba(247,112,137, 0.6) !important;
    border-color: rgba(247,112,137, 0.6) !important;
}
.popover .btn{
    margin-bottom: 15px;
    width: 49%;
    display: inline-block;
    background: rgba(123,121,127, 1);
    border-radius: 10px;
    border: 1px #000 solid;
    color: #000;
    text-align: center;
    transition: 0.3s;
}
.popover .btn:hover{
    background:  rgba(107,98,108, 0.6);
    border-color: rgba(107,98,108, 0.6);
    color: #000 !important;
}
</style>
