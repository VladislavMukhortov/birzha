<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="8" lg="6">

                        <h1 class="section_title">Список сообщений</h1>

                        <div v-if="messages.length">
                            <b-list-group>
                                <template v-for="item in messages">
                                    <b-list-group-item v-bind:to="'/messages/'+item.user_id">
                                        <p>
                                            <strong>{{ item.user_name }}</strong><span>, {{ item.company_name }}</span>
                                        </p>
                                        <span>{{ item.text }}</span>
                                    </b-list-group-item>
                                </template>
                            </b-list-group>
                        </div>
                        <div v-else>Сообщений еще нет</div>

                        <pre>{{ messages }}</pre>

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
            title: 'Messages | site.com',
            meta: [{ hid: 'description', name: 'description', content: '' }]
        }
    },

    data() {
        return {
            messages: {}
        }
    },

    async asyncData ({ $axios }) {
        const messages = await $axios.$get('/api/messages/dialogs/index').then((res) => {
            return res;
        });

        return { messages: messages };
    }
};
</script>



<style lang='scss'>
.market-content {
    margin: 1rem 0 0 0;
}
</style>
