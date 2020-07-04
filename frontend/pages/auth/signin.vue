// Страница входа

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="4">
                        
                        <h1 class="section_title text-center">Sign in to Grain Market</h1>
                        <div class="section-signin">
                            <b-alert
                                variant="danger"
                                dismissible
                                fade
                                v-bind:show="showAlertError"
                                v-on:dismissed="showAlertError=false">Incorrect login or password!</b-alert>

                            <b-form novalidate v-on:submit.prevent="onSubmit">
                                <div class="wrapper-signin-inp">
                                    <b-form-group label="Email address or phone">
                                        <b-input required type="text" v-model="login" tabindex="1"></b-input>
                                    </b-form-group>
                                </div>
                                <div class="wrapper-signin-inp">
                                <b-form-group label="Password">
                                    <b-link class="signin-password-reset-link" to="/auth/password-reset">Forgot password?</b-link>
                                    <b-input required type="password" v-model="password" tabindex="2"></b-input>
                                </b-form-group>
                                </div>
                                <div class="wrapper-signin">
                                <b-button
                                    type="submit"
                                    variant="success"
                                    block
                                    tabindex="3"
                                    v-bind:class="{ 'disabled': !isActiveSubmitBtn }">Signin</b-button>
                                </div>

                            </b-form>

                            <div class="signin-to-signup text-center">New to Grain Market? <br> <b-link to="/auth/signup">Create an account.</b-link></div>
                        </div>
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
            login: '',              // логин
            password: '',           // пароль
            showAlertError: false,  // некорректный логин или пароль
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

            this.showAlertError = false;

            let _params = new URLSearchParams();
            _params.append('login', this.login);
            _params.append('password', this.password);

            let res = await this.$axios.$post('/api/auth/signin/index', _params).then((res) => {
                return res;
            }).catch((error) => {
                return {result:'error'};
            });

            if (res.result === 'success') {
                // $nuxt.$auth.$storage.setUniversal('token', res.access_token, false);
                // $nuxt.$auth.$storage.setState('loggedIn', true);

                this.$auth.$storage.setUniversal('companyName', res.company);
                this.$auth.$storage.setUniversal('userName', res.name);
                this.$auth.$storage.setUniversal('userPhone', res.phone);
                this.$auth.$storage.setUniversal('userEmail', res.email);

                this.$auth.setUserToken(res.access_token);

                this.$router.push('/market');
            } else {
                this.showAlertError = true;
            }
        },
    },
};
</script>



<style lang='scss'>
.section-signin{
    border: 1px solid gray;
    border-radius: 10px;
    margin: 5px;
    padding: 10px;
    width: 100%;
}
.signin-password-reset-link {
    position: absolute;
    top: 0;
    right: 0;
}

.signin-to-signup {
    padding: 1rem;
}
.wrapper-signin-inp input{
    border-radius: 10px !important;
}
.wrapper-signin .btn{
    width: 100%;
    display: inline-block;
    background: rgba(76,217,100, 1) !important;
    border-radius: 10px !important;
    color: #000 !important;
    text-align: center;
    transition: 0.3s;
}
.wrapper-signin .btn:hover{
    background:  #6ff1a1 !important;
    border-color: #6ff1a1 !important;
}
</style>
