<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="6" lg="4">

                        <template v-if="showAlertErrorToken">

                            <h1 class="section_title text-center">Change password</h1>

                            <b-alert
                                variant="danger"
                                fade
                                v-bind:show="showAlertErrorToken">Incorrect reset token!</b-alert>

                            <b-link class="btn btn-primary btn-block" to="/auth/signin">Return to sign in</b-link>

                        </template>
                        <template v-else>

                            <h1 class="section_title text-center">Change password for {{ email }}</h1>

                            <b-alert
                                variant="danger"
                                fade
                                v-bind:show="showAlertErrorPassword"
                                v-on:dismissed="showAlertErrorPassword=false">{{ textAlertErrorPassword }}</b-alert>

                            <b-form v-on:submit.prevent="onSubmit">

                                <b-form-group label="Введите пароль" label-class="required">
                                    <b-input required v-model="password" type="password"></b-input>
                                </b-form-group>

                                <b-form-group label="Подтвердите пароль" label-class="required">
                                    <b-input required v-model="passwordConfirm" type="password"></b-input>
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
</style>
