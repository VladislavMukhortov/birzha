<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="6" lg="4">                        
                        <h1 class="section_title text-center">Reset your password</h1>
                        <div class="reset-container">
                            <template v-if="showAlertSuccess">

                                <b-alert
                                    variant="success"
                                    fade
                                    v-bind:show="showAlertSuccess">Check your email for a link to reset your password. If it doesn’t appear within a few minutes, check your spam folder.</b-alert>
                                <div class="wrapper-reset-btn">
                                    <b-link class="btn btn-success btn-block" to="/auth/signin">Return to sign in</b-link>
                                </div>

                            </template>
                            <template v-else>

                                <p class="text-center">Enter your email address and we will send you a link to reset your password.</p>

                                <b-alert
                                    variant="warning"
                                    fade
                                    v-bind:show="showAlertError">Ой! Нет пользователя с этим адресом электронной почты</b-alert>

                                <b-form v-on:submit.prevent="onSubmit">
                                    <div class="wrapper-reset-inp">
                                        <b-form-group label="Email address">
                                                <b-input required type="text" v-model="email" placeholder="Enter your email address"></b-input>  
                                        </b-form-group>
                                    </div>
                                    <div class="wrapper-reset-btn">
                                        <b-button
                                            type="submit"
                                            variant="success"
                                            block
                                            v-bind:class="{ 'disabled': !isActiveSubmitBtn }">Send password reset email</b-button>
                                    </div>

                                </b-form>

                            </template>
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
            title: 'Reset your password | site.com',
            meta: [{ hid: 'description', name: 'description', content: '' }]
        }
    },

    data() {
        return {
            email: '',                  // почтовый ящик
            showAlertError: false,      // уведомление если почты нет
            showAlertSuccess: false,    // уведомление если почта есть
        }
    },

    computed: {
        /**
         * Проверка наличия email для блокировки кнопки "Submit"
         * @return boolean
         */
        isActiveSubmitBtn() {
            return this.checkData();
        },

    },

    methods: {
        /**
         * Проверка наличия email для блокировки кнопки "Submit"
         * @return boolean
         */
        checkData() {
            return (this.email.length) ? true : false;
        },

        /**
         * Отправка данных для сброса пароля
         */
        async onSubmit() {
            if (!this.checkData()) {
                return;
            }

            this.showAlertError = false;
            this.showAlertSuccess = false;

            var _params = new URLSearchParams();
            _params.append('email', this.email);

            let res = await this.$axios.$post('/api/auth/request-password-reset/index', _params).then((res) => {
                return res;
            }).catch((error) => {
                return {result:'error'};
            });

            if (res.result === 'success') {
                if (res.send) {
                    // сообщение отправлено только что
                } else {
                    // сообщение было отправлено при предыдущем обращении
                }
                this.showAlertSuccess = true;
            } else {
                this.showAlertError = true;
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

.reset-container{
    border: 1px solid grey;
    border-radius: 10px !important;
    padding: 10px;
}

.wrapper-reset-inp input{
    border-radius: 10px !important;
}
.wrapper-reset-btn .btn{
    width: 100%;
    display: inline-block;
    background: rgba(76,217,100, 1) !important;
    border-radius: 10px !important;
    color: #000 !important;
    text-align: center;
    transition: 0.3s;
}
.wrapper-reset-btn .btn:hover{
    color: #000 !important;
    background:  #6ff1a1 !important;
    border-color: #6ff1a1 !important;
}
</style>
