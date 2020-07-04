<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">

                    <b-col cols="12" md="8" lg="6">

                        <h1 class="section_title">Редактирование профиля</h1>

                        <b-alert
                            variant="success"
                            dismissible
                            fade
                            v-bind:show="showAlertEdit"
                            v-on:dismissed="showAlertEdit=false">Data updated!</b-alert>

                        <b-form v-on:submit.prevent="onSubmitFormEdit">

                            <b-form-group label-for="edit-member-name" label="ФИО" label-class="required">
                                <b-input
                                    required
                                    id="edit-member-name"
                                    v-model="memberName"
                                    v-bind:state="memberNameState"
                                    type="text"></b-input>
                                <b-form-invalid-feedback>{{ memberNameStateText }}</b-form-invalid-feedback>
                            </b-form-group>

                            <b-form-group label-for="edit-member-phone" label="Ваш телефон" label-class="required">
                                <b-input
                                    required
                                    id="edit-member-phone"
                                    v-model="memberPhone"
                                    v-bind:readonly="memberVerifyPhone"
                                    v-bind:state="memberPhoneState"
                                    type="text"></b-input>
                                <b-form-invalid-feedback>{{ memberPhoneStateText }}</b-form-invalid-feedback>
                            </b-form-group>

                            <b-form-group label-for="edit-member-email" label="Email address" label-class="required">
                                <b-input
                                    required
                                    id="edit-member-email"
                                    v-model="memberEmail"
                                    v-bind:readonly="memberVerifyEmail"
                                    v-bind:state="memberEmailState"
                                    type="text"></b-input>
                                <b-form-invalid-feedback>{{ memberEmailStateText }}</b-form-invalid-feedback>
                            </b-form-group>

                            <b-form-group label-for="edit-member-position" label="Должность в компании">
                                <b-input
                                    id="edit-member-position"
                                    v-model="memberPosition"
                                    v-bind:state="memberPositionState"
                                    type="text"></b-input>
                                <b-form-invalid-feedback>{{ memberPositionStateText }}</b-form-invalid-feedback>
                            </b-form-group>
                            <div class="custom-btn-save">    
                                <b-button type="submit" variant="success">Сохранить</b-button>
                            </div>
                        </b-form>

                    </b-col>

                    <b-col cols="12" md="8" lg="6">

                        <h1 class="section_title">Данные Контрагента</h1>
                        <div class="custom-btn">
                            <nuxt-link class="btn" to="/profile/verified">Редактировать</nuxt-link>
                        </div>
                        

                    </b-col>

                </b-row>
                <div class="change_pass">
                    <b-row>
                        <b-col cols="12" md="8" lg="6">

                            <h1 class="section_title">Задать пароль</h1>

                            <p>Обновлен: {{ changePasswordAt }}</p>
                            <div class="custom-btn">
                                <b-button v-b-modal.js-modal-change-password>Изменить</b-button>
                            </div>

                        </b-col>
                    </b-row>
                </div>

            </b-container>
        </section>

        <b-modal
            id="js-modal-change-password"
            title="Изменить пароль"
            hide-footer
            size="sm">

            <div class="d-block">
                <b-form v-on:submit.prevent="onSubmitFormChangePassword">
                    <div class="custom-modal">
                        <b-alert
                            variant="danger"
                            fade
                            v-bind:show="showAlertErrorPassword">{{ textAlertErrorPassword }}</b-alert>

                        <b-form-group label="Введите пароль" label-class="required">
                            <b-input required v-model="password" type="password"></b-input>
                        </b-form-group>

                        <b-form-group label="Подтвердите пароль" label-class="required">
                            <b-input required v-model="passwordConfirm" type="password"></b-input>
                        </b-form-group>

                        <b-button type="submit" variant="success" block>Сохранить</b-button>
                    </div>
                </b-form>
            </div>

        </b-modal>

    </main>

</template>



<script>
export default {

    head() {
        return {
            title: 'Profile | site.com',
            meta: [{ hid: 'description', name: 'description', content: '' }]
        };
    },

    data() {
        return {
            company: {},

            memberName: '',             // имя пользователя
            memberPhone: '',            // телефон
            memberEmail: '',            // почтовый ящик
            memberPosition: '',         // должность
            memberVerifyEmail: false,   // статус подтверждения почты
            memberVerifyPhone: false,   // статус подтверждения телефона

            changePasswordAt: '',       // дата последнего изменния пароля
            password: '',               // пароль
            passwordConfirm: '',        // подтверждение пароля
            showAlertErrorPassword: false,

            showAlertEdit: false,           // уведомление об успешном изменении личных данных
            memberNameState: null,          // состояние - имя пользователя
            memberPhoneState: null,         // состояние - телефон пользователя
            memberEmailState: null,         // состояние - почта пользователя
            memberPositionState: null,      // состояние - должность пользователя
            memberNameStateText: '',        // текст состояния - имя пользователя
            memberPhoneStateText: '',       // текст состояния - телефон пользователя
            memberEmailStateText: '',       // текст состояния - почта пользователя
            memberPositionStateText: '',    // текст состояния - должность пользователя
            textAlertErrorPassword: '',
        };
    },

    async asyncData ({ $axios }) {

        let [user, company] = await Promise.all([
            $axios.$get('/api/user/my/index').then((res) => {
                return res;
            }),

            $axios.$get('/api/company/my/index').then((res) => {
                return res;
            })
        ]);

        return {
            company: company,
            memberName: user.name || '',
            memberPhone: user.phone || '',
            memberEmail: user.email || '',
            memberPosition: user.position || '',
            memberVerifyEmail: user.verify_email || false,
            memberVerifyPhone: user.verify_phone || false,
            changePasswordAt: user.change_password_at || '',
        };
    },

    methods: {
        /*
         *Отправка формы на изменение информации о пользователе
         */
        async onSubmitFormEdit() {
            this.showAlertEdit = false;
            this.memberNameState = '';
            this.memberPhoneState = '';
            this.memberEmailState = '';
            this.memberPositionState = '';

            let _params = new URLSearchParams();
            _params.append('member_name', this.memberName);
            _params.append('member_phone', this.memberPhone);
            _params.append('member_email', this.memberEmail);
            _params.append('member_position', this.memberPosition);

            let res = await this.$axios.$post('/api/user/edit/index', _params).then((res) => {
                return res;
            });

            if (res.success) {
                this.$auth.$storage.setUniversal('userName', res.name);
                this.$auth.$storage.setUniversal('userPhone', res.phone);
                this.$auth.$storage.setUniversal('userEmail', res.email);

                this.showAlertEdit = true;
            } else {
                if (res.error) {
                    this.memberNameState = (res.error.member_name) ? false : true;
                    this.memberPhoneState = (res.error.member_phone) ? false : true;
                    this.memberEmailState = (res.error.member_email) ? false : true;
                    this.memberPositionState = (res.error.member_position) ? false : true;

                    this.memberNameStateText = res.error.member_name || '';
                    this.memberPhoneStateText = res.error.member_phone || '';
                    this.memberEmailStateText = res.error.member_email || '';
                    this.memberPositionStateText = res.error.member_position || '';
                }
            }
        },


        /**
         * Отправка формы на изменения пароля
         */
        async onSubmitFormChangePassword() {
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
@media (max-width: 768px){
    .custom-btn button{
        width: 100%;
        display: inline-block;
        background: rgba(123,121,127, 1) !important;
        border-radius: 10px !important;
        border: 1px #000 solid !important;
        color: #000 !important;
        text-align: center;
        transition: 0.3s;
    }
    .custom-btn button:hover{
        background:  rgba(107,98,108, 0.6) !important;
        border-color: rgba(107,98,108, 0.6) !important;
        color: #000 !important;
    }
    .custom-btn a{
        width: 100%;
        display: inline-block;
        background: rgba(123,121,127, 1) !important;
        border-radius: 10px !important;
        border: 1px #000 solid !important;
        color: #000 !important;
        text-align: center;
        transition: 0.3s;
    }
    .custom-btn a:hover{
        background:  rgba(107,98,108, 0.6) !important;
        border-color: rgba(107,98,108, 0.6) !important;
        color: #000 !important;
    }
    // .change_pass .row .col-md-8{
    //     width: 100%;
    //     float: left;
    // }
    .change_pass .row .col-md-8{
        margin: 0 auto;
        
    }
}
@media (min-width: 1024px){
    .custom-btn button{
        width: 45%;
        display: inline-block;
        background: rgba(123,121,127, 1) !important;
        border-radius: 10px !important;
        border: 1px #000 solid !important;
        color: #000 !important;
        text-align: center;
        transition: 0.3s;
    }
    .custom-btn button:hover{
        background:  rgba(107,98,108, 0.6) !important;
        border-color: rgba(107,98,108, 0.6) !important;
        color: #000 !important;
    }
    .custom-btn a{
        width: 45%;
        display: inline-block;
        background: rgba(76,217,100, 1) !important;
        border-radius: 10px !important;
        color: #000 !important;
        text-align: center;
        transition: 0.3s;
    }
    .custom-btn a:hover{
        background:  #6ff1a1 !important;
        border-color: #6ff1a1 !important;
        color: #000 !important;
    }
    .custom-btn-save button{
        width: 45%;
        display: inline-block;
        background: rgba(76,217,100, 1) !important;
        border-radius: 10px !important;
        color: #000 !important;
        text-align: center;
        transition: 0.3s;
    }
    .custom-btn-save button:hover{
        background:  #6ff1a1 !important;
        border-color: #6ff1a1 !important;
        color: #000 !important;
    }
    
}
.custom-modal input{
    border-radius: 10px;
}

.custom-modal .btn{
    width: 100%;
    display: inline-block;
    background: rgba(76,217,100, 1) !important;
    border-radius: 10px !important;
    color: #000 !important;
    text-align: center;
    transition: 0.3s;
}

.custom-modal .btn:hover{
    background:  #6ff1a1 !important;
    border-color: #6ff1a1 !important;
    color: #000 !important;
}

</style>
