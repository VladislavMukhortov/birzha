<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="6" lg="4">

                        <template v-if="show_alert_error">

                            <h1 class="section_title text-center">Change password</h1>

                            <b-alert
                                variant="danger"
                                fade
                                v-bind:show="show_alert_error">Incorrect reset token!</b-alert>

                            <nuxt-link class="btn btn-primary btn-block" to="/auth/signin">Return to sign in</nuxt-link>

                        </template>
                        <template v-else>

                            <h1 class="section_title text-center">Change password for {{ email }}</h1>

                            <b-form v-on:submit.prevent="onSubmit">

                                <b-form-group label-for="change-password" label="Введите пароль" label-class="required">
                                    <b-input
                                        required
                                        id="change-password"
                                        v-model="password"
                                        type="password"></b-input>
                                </b-form-group>

                                <b-form-group label-for="change-password-confirm" label="Подтвердите пароль" label-class="required">
                                    <b-input
                                        required
                                        id="change-password-confirm"
                                        v-model="passwordConfirm"
                                        type="password"></b-input>
                                </b-form-group>

                                <b-button type="submit" variant="primary" block>Change password</b-button>
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
    auth: false,

    head() {
        return {
            title: 'Change password | site.com',
        }
    },

    data() {
        return {
            success: null,
            company: '',
            name: '',
            phone: '',
            email: '',
            access_token: '',

            show_alert_error: false,    // некорректный токен для изменения пароля

            password: '',               // пароль
            passwordConfirm: '',        // подтверждение пароля
        }
    },

    async asyncData({ $axios, params }) {
        let _params = {
            params: {
                token: params.token
            }
        };

        let res = await $axios.$get('/api/auth/password-reset/index', _params).then((res) => {
            return res;
        });

        return {
            success: res.success,
            show_alert_error: !res.success,
            company: res.company || '',
            name: res.name || '',
            phone: res.phone || '',
            email: res.email || '',
            access_token: res.access_token || ''
        };
    },

    mounted() {
        if (this.success) {
            this.$auth.$storage.setUniversal('companyName', this.company);
            this.$auth.$storage.setUniversal('userName', this.name);
            this.$auth.$storage.setUniversal('userPhone', this.phone);
            this.$auth.$storage.setUniversal('userEmail', this.email);

            this.$auth.setUserToken(this.access_token);
        } else {
            this.show_alert_error = true;
        }
    },


    methods: {
        /**
         * Отправка формы на изменения пароля
         */
        async onSubmit() {
            if (this.password !== this.passwordConfirm) {
                /**
                 * TODO: показать уведомление что пароли не совпадают
                 */

                return;
            }

            let _params = new URLSearchParams();
            _params.append('password', this.password);
            _params.append('confirm', this.passwordConfirm);

            let res = await this.$axios.$post('/api/user/change-password/index', _params).then((res) => {
                return res;
            });

            if (res.success) {
                this.$auth.setUserToken(res.access_token);

                setTimeout(function() {
                    $nuxt.$router.push('/profile');
                }, 200);
                // уведомление что пароль изменен
            } else {
                // ошибка
            }
        },
    },
};
</script>



<style lang='scss'>

</style>
