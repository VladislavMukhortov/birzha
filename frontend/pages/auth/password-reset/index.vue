<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="6" lg="4">

                        <h1 class="section_title text-center">Reset your password</h1>

                        <template v-if="show_alert_success">

                            <b-alert
                                variant="success"
                                fade
                                v-bind:show="show_alert_success">Check your email for a link to reset your password. If it doesn’t appear within a few minutes, check your spam folder.</b-alert>

                            <nuxt-link class="btn btn-primary btn-block" to="/auth/signin">Return to sign in</nuxt-link>

                        </template>
                        <template v-else>

                            <p class="text-center">Enter your email address and we will send you a link to reset your password.</p>

                            <b-alert
                                variant="warning"
                                dismissible
                                fade
                                v-bind:show="show_alert_error"
                                v-on:dismissed="show_alert_error=false">Ой! Нет пользователя с этим адресом электронной почты</b-alert>

                            <b-form v-on:submit.prevent="onSubmit">

                                <b-form-group label-for="reset-password-email">
                                    <b-input
                                        required
                                        id="reset-password-email"
                                        type="text"
                                        v-model="email"
                                        placeholder="Enter your email address"></b-input>
                                </b-form-group>

                                <b-button
                                    type="submit"
                                    block
                                    variant="primary">Send password reset email</b-button>

                            </b-form>

                        </template>

                    </b-col>
                </b-row>
            </b-container>
        </section>
    </main>

</template>



<script>
export default {
    auth: 'guest',

    head() {
        return {
            title: 'Reset your password | site.com',
            meta: [{ hid: 'description', name: 'description', content: '' }]
        }
    },

    data() {
        return {
            email: '',                  // почтовый ящик
            show_alert_error: false,    // уведомление если почты нет
            show_alert_success: false,  // уведомление если почта есть
            text_alert_success: '',     // текст для положительного уведомления
        }
    },

    methods: {
        /**
         * Отправка данных для сброса пароля
         */
        async onSubmit() {
            if (this.email.length <= 0) {
                return;
            }

            this.show_alert_error = false;
            this.show_alert_success = false;

            var _params = new URLSearchParams();
            _params.append('email', this.email);

            let res = await this.$axios.$post('/api/auth/request-password-reset/index', _params).then((res) => {
                return res;
            });

            if (res.email) {
                if (res.send) {
                    // сообщение отправлено только что
                    this.text_alert_success = 'Готово! Проверьте свою почту';
                } else {
                    // сообщение было отправлено при предыдущем обращении
                    this.text_alert_success = 'Готово! Проверьте свою почту';
                }

                this.show_alert_success = true;
            } else {
                this.show_alert_error = true;
            }
        },
    },

};
</script>



<style lang='scss'>
.signin-password-reset {
    position: absolute;
    top: 0;
    right: 0;
}
</style>
