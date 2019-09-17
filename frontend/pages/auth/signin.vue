<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="4">

                        <h1 class="section_title text-center">Sign in to Grain Market</h1>


                        <b-alert
                            variant="danger"
                            dismissible
                            fade
                            v-bind:show="show_alert_error"
                            v-on:dismissed="show_alert_error=false">Incorrect login or password!</b-alert>


                        <b-form novalidate v-on:submit.prevent="onSubmit">

                            <b-form-group label="Email address or phone" label-for="signin-login">
                                <b-input
                                    id="signin-login"
                                    type="text"
                                    v-model="login"
                                    v-bind:state="login_state"
                                    tabindex="1"></b-input>
                                <b-form-invalid-feedback>Login is incorrect</b-form-invalid-feedback>
                            </b-form-group>

                            <b-form-group label="Password" label-for="signin-password">
                                <nuxt-link class="signin-password-reset-link" to="/auth/password-reset">Forgot password?</nuxt-link>
                                <b-input
                                    id="signin-password"
                                    type="password"
                                    v-model="password"
                                    v-bind:state="password_state"
                                    tabindex="2"></b-input>
                                <b-form-invalid-feedback>Password is incorrect</b-form-invalid-feedback>
                            </b-form-group>

                            <b-button
                                type="submit"
                                variant="primary"
                                block
                                tabindex="3"
                                v-bind:class="{ 'disabled': !isActiveSubmitBtn }">Signin</b-button>

                        </b-form>

                        <div class="signin-to-signup text-center">New to Grain Market? <nuxt-link to="/auth/signup">Create an account.</nuxt-link></div>

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
            title: 'Sign in to Grain Market | site.com',
            meta: [{ hid: 'description', name: 'description', content: '' }]
        }
    },

    data() {
        return {
            login: '',                  // логин
            password: '',               // пароль
            login_state: '',            // состояние - логин
            password_state: '',         // состояние - пароль
            show_alert_error: false,    // некорректный логин или пароль
        }
    },

    computed: {
        /**
         * Проверка наличия логина и пароля для блокировки кнопки "Submit"
         * @return boolean
         */
        isActiveSubmitBtn() {
            return this.checkData();
        },

    },

    methods: {
        /**
         * Проверка наличия логина и пароля для блокировки кнопки "Submit"
         * @return boolean
         */
        checkData() {
            return (this.login.length && this.password.length) ? true : false;
        },



        /**
         * Отправка данных для авторизации
         */
        async onSubmit() {
            if (!this.checkData()) {
                return;
            }

            // проверка заполнености логина и пароля
            this.login_state = (this.login.length <= 0) ? false : true;
            this.password_state = (this.password.length <= 0) ? false : true;
            if (this.login_state == false || this.password_state == false) {
                return;
            }

            let _params = new URLSearchParams();
            _params.append('login', this.login);
            _params.append('password', this.password);

            let res = await this.$axios.$post('/api/auth/signin/index', _params).then((res) => {
                return res;
            });
            // .catch((error) => {
                // console.log(error);
            // })

            if (res.success) {
                // $nuxt.$auth.$storage.setUniversal('token', res.access_token, false);
                // $nuxt.$auth.$storage.setState('loggedIn', true);

                this.$auth.$storage.setUniversal('companyName', res.company);
                this.$auth.$storage.setUniversal('userName', res.name);
                this.$auth.$storage.setUniversal('userPhone', res.phone);
                this.$auth.$storage.setUniversal('userEmail', res.email);

                this.$auth.setUserToken(res.access_token);

                this.$router.push('/market');
            } else {
                this.show_alert_error = true;
            }
        },
    },
};
</script>



<style lang='scss'>
.signin-password-reset-link {
    position: absolute;
    top: 0;
    right: 0;
}

.signin-to-signup {
    padding: 1rem;
}
</style>
