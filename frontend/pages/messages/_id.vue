<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="10" lg="8">

                        <h1 class="section_title">Список сообщений</h1>

                        <pre>{{user}}</pre>
                        <pre>{{messages}}</pre>


                        <div>
                            <p>Написать сообщение</p>

                            <b-form-group label-for="message-input">
                                <b-input
                                    id="message-text-input"
                                    type="text"
                                    v-model="messageText"
                                    v-bind:state="messageTextState"></b-input>
                                <b-form-invalid-feedback>Введите текст сообщения</b-form-invalid-feedback>
                            </b-form-group>

                            <b-button
                                type="button"
                                variant="primary"
                                v-on:click="sendTextMessages"
                                v-bind:class="{ 'disabled': !isActiveSubmitBtn }">Отправить</b-button>
                        </div>


                        <p class="text-center">
                            <nuxt-link to="/messages">TO Back</nuxt-link>
                        </p>


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
            meta: [
                { hid: 'description', name: 'description', content: '' }
            ]
        }
    },

    validate({ params }) {
        return /^\d+$/.test(params.id)
    },

    /**
     * Параметры в объекте, что бы избежать ошибок при 404
     */
    data() {
        return {
            user: {},               // пользователь с которым ведется переписка
            messages: {},           // список сообщений
            messageText: '',        // текст сообщения который отправляется собеседнику
            messageTextState: '',   // состояние текста сообщения
        }
    },

    computed: {
        /**
         * Проверка наличия текста для блокировки кнопки "Submit"
         * @return boolean
         */
        isActiveSubmitBtn() {
            return (this.messageText.length) ? true : false;
        },

    },

    /**
     * получаем данные о объявлении
     * @return object
     */
    async asyncData ({ $axios, params }) {
        let _param = {params: {
            id: params.id
        }};

        let [user, messages] = await Promise.all([
            $axios.$get('/api/user/show/index', _param).then((res) => {
                return res;
            }),

            $axios.$get('/api/messages/messages/index', _param).then((res) => {
                return res;
            })
        ]);

        return {
            user: user,
            messages: messages,
        };
    },

    methods: {

        /**
         * отправляем текст собеседнику
         */
        async sendTextMessages() {
            this.memberNameState = '';

            if (!this.messageText.length) {
                this.memberNameState = false;
            }

            let _params = new URLSearchParams();
            _params.append('text', this.messageText);
            _params.append('id', this.user.id);

            let res = await this.$axios.$post('/api/messages/send-text/index', _params).then((res) => {
                return res;
            });

            if (res.success) {
                // сообщение отправленно
                this.messageText = '';
            }
        },
    },

};
</script>



<style lang='scss'>

</style>
