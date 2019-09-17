<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row>
                    <b-col cols="12" md="8">

                        <h1 class="section_title">My orders </h1>

                        <pre>{{orders}}</pre>

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
            title: 'My orders | site.com',
            meta: [{ hid: 'description', name: 'description', content: '' }]
        }
    },

    data() {
        return {
            orders: {}
        }
    },

    async asyncData ({ $axios }) {
        const orders = await $axios.$get('/api/messages/dialogs/index').then((res) => {
            return res;
        });

        return { orders: orders.data };
    }
};
</script>



<style lang='scss'>

</style>
