<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="6" lg="4">
                        <div class="reset-container">

                            <template v-if="showAlertErrorToken">

                                <h1 class="section_title text-center">Change password</h1>
                                
                                <b-alert
                                    variant="danger"
                                    fade
                                    v-bind:show="showAlertErrorToken">Incorrect reset token!</b-alert>
                                <div class="wrapper-incprrect-btn">
                                    <b-link class="btn btn-block" to="/auth/signin">Return to sign in</b-link>
                                </div>
                            </template>
                            <template v-else>

                                <h1 class="section_title text-center">Change password for {{ email }}</h1>

                                <b-alert
                                    variant="danger"
                                    fade
                                    v-bind:show="showAlertErrorPassword"
                                    v-on:dismissed="showAlertErrorPassword=false">{{ textAlertErrorPassword }}</b-alert>

                                <b-form v-on:submit.prevent="onSubmit">

                                    <div class="wrapper-reset-inp">
                                        <b-form-group label="Введите пароль" label-class="required">
                                            <b-input required v-model="password" type="password"></b-input>
                                        </b-form-group>
                                    </div>

                                    <div class="wrapper-reset-inp">
                                        <b-form-group label="Подтвердите пароль" label-class="required">
                                            <b-input required v-model="passwordConfirm" type="password"></b-input>
                                        </b-form-group>
                                    </div>
                                    <div class="wrapper-reset-btn">
                                        <b-button type="submit" variant="success" block>Change password</b-button>
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

            showAlertErrorToken: false,     // некорректный токен для изменения пароля
            showAlertErrorPassword: false,  // пароли не совпадают
            textAlertErrorPassword: '',     // текст ошибки

            password: '',           // пароль
            passwordConfirm: '',    // подтверждение пароля
        }
    },

    async asyncData({ $axios, params }) {
        let _params = {params: {
            token: params.token
        }};

        let res = await $axios.$get('/api/auth/password-reset/index', _params).then((res) => {
            return res;
        }).catch((error) => {
            return {result:'error'};
        });

        if (res.result === 'success') {
            return {
                success: true,
                company: res.company || '',
                name: res.name || '',
                phone: res.phone || '',
                email: res.email || '',
                access_token: res.access_token || ''
            };
        }

        return { success: false };
    },

    mounted() {
        if (this.success) {
            this.$auth.$storage.setUniversal('companyName', this.company);
            this.$auth.$storage.setUniversal('userName', this.name);
            this.$auth.$storage.setUniversal('userPhone', this.phone);
            this.$auth.$storage.setUniversal('userEmail', this.email);

            this.$auth.setUserToken(this.access_token);
        } else {
            this.showAlertErrorToken = true;
        }
    },


    methods: {
        /**
         * Отправка формы на изменения пароля
         */
        async onSubmit() {
            this.showAlertErrorPassword = false;

            if (this.password !== this.passwordConfirm) {
                this.textAlertErrorPassword = 'Пароли не совпадают';
                this.showAlertErrorPassword = true;
                return;
            }

            let _params = new URLSearchParams();
            _params.append('password', this.password);
            _params.append('confirm', this.passwordConfirm);

            let res = await this.$axios.$post('/api/user/change-password/index', _params).then((res) => {
                return res;
            }).catch((error) => {
                return {result:'error'};
            });

            if (res.result === 'success') {
                this.$auth.setUserToken(res.access_token);

                setTimeout(function() {
                    $nuxt.$router.push('/profile');
                }, 200);
            } else {
                this.textAlertErrorPassword = 'Ой! Попробуйте позже.';
                this.showAlertErrorPassword = true;
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
.wrapper-incprrect-btn .btn{
    border-radius: 10px !important;
    width: 100%;
    display: inline-block;
    background: rgba(123,121,127, 1) !important;
    border-radius: 10px !important;
    border: 1px #000 solid !important;
    color: #000 !important;
    text-align: center;
    transition: 0.3s;
}
.wrapper-incprrect-btn .btn:hover{
    background:  rgba(107,98,108, 0.6) !important;
    border-color: rgba(107,98,108, 0.6) !important;
    color: #000 !important;
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
