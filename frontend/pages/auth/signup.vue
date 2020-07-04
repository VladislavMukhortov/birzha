// Страница регистрации

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row>
                    <b-col cols="12" md="8">

                        <h1 class="section_title">Join Grain Market</h1>
                        <div class="section-signin">
                            <h2 class="section_subtitle">Добро пожаловать!</h2>
                            <p>Для успешной регистрации укажите информацию о себе и организации, которую представляете. Для вашего удобства, мы разбили процесс регистрации на шаги.</p>


                            <!-- STEP NUMBER -->
                            <div class="wrapper-steps">
                                <div class="signup-steps">
                                    <div class="signup-steps-item _step_1" v-bind:class="{ '_active': step_1 }" v-on:click="toOneStep">
                                        <strong>Шаг 1</strong>
                                        <div>Персональная информация</div>
                                    </div>
                                    <div class="signup-steps-item _step_2" v-bind:class="{ '_active': step_2 }">
                                        <strong>Шаг 2</strong>
                                        <div>Информация об организации</div>
                                    </div>
                                </div>
                            </div>


                            <!-- STEP 0 -->
                            <div class="signup-step" v-bind:class="{ '_active': step_0 }">
                                <b-button
                                    
                                    v-on:click="toOneStep">Далее</b-button>
                            </div>


                            <!-- STEP 1 -->
                            <div class="signup-step" v-bind:class="{ '_active': step_1 }">

                                <b-form-group label="Ваше имя" label-class="required">
                                    <b-input type="text" v-model="memberName" v-bind:state="memberNameState"></b-input>
                                </b-form-group>

                                <b-form-group label="Ваш телефон" label-class="required">
                                    <b-input type="text" v-model="memberPhone" v-bind:state="memberPhoneState"></b-input>
                                    <b-form-text>Номер телефона через +</b-form-text>
                                    <b-form-invalid-feedback>Этот номер телефона уже занят</b-form-invalid-feedback>
                                </b-form-group>

                                <b-form-group label="Email address" label-class="required">
                                    <b-input type="email" v-model="memberEmail" v-bind:state="memberEmailState"></b-input>
                                    <b-form-invalid-feedback>Эта электронная почта уже занята</b-form-invalid-feedback>
                                </b-form-group>

                                <b-button
                                    
                                    v-bind:class="{ 'disabled': !checkFirstStepCompleted }"
                                    v-on:click="toTwoStep">Далее</b-button>

                            </div>


                            <!-- STEP 2 -->
                            <div class="signup-step" v-bind:class="{ '_active': step_2 }">

                                <div class="signup-input-info">
                                    <div>Имя: <b>{{memberName}}</b></div>
                                    <div>Телефон: <b>{{memberPhone}}</b></div>
                                    <div>Почтовый ящик: <b>{{memberEmail}}</b></div>
                                </div>

                                <b-form-group label="Company Name" label-class="required">
                                    <b-input type="text" v-model="companyName"></b-input>
                                </b-form-group>

                                <b-form-group label="SWIFT" label-class="required">
                                    <b-input type="text" v-model="companySwift"></b-input>
                                </b-form-group>

                                <b-form-group label="ACC / IBAN" label-class="required">
                                    <b-input type="text" v-model="companyIban"></b-input>
                                </b-form-group>

                                <hr>

                                <div class="small">By clicking «Create an account» below, you agree to our <nuxt-link target="_blank" to="/site/terms">Terms of Service</nuxt-link> and <nuxt-link target="_blank" to="/site/privacy">Privacy Statement</nuxt-link>. We'll occasionally send you account-related emails.</div>

                                <hr>
                                <div class="wrapper-signup">
                                    <b-button
                                        variant="success"
                                        class=""
                                        v-bind:class="{ 'disabled': !checkTwoStepCompleted }"
                                        v-on:click="onSubmit">Create an account</b-button>
                                </div>

                            </div>
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
            title: 'Join Grain Market | site.com',
            meta: [{ hid: 'description', name: 'description', content: '' }]
        }
    },

    data() {
        return {
            step_0: true,       // шаг 0 - начало
            step_1: false,      // шаг 1 - персональные данные
            step_2: false,      // шаг 2 - данные компании

            memberName:   '',   // представитель - имя
            memberPhone:  '',   // представитель - телефон
            memberEmail:  '',   // представитель - почта
            companyName:  '',   // компания - название
            companySwift: '',   // компания - swift
            companyIban:  '',   // компания - acconunt number

            memberNameState: null,  // состояние для проверки имени
            memberPhoneState: null, // состояние для проверки телефона
            memberEmailState: null, // состояние для проверки почты


        }
    },

    computed: {
        /**
         * проверка, завершен ли первый шаг, что бы перейти к следующему
         * @return boolean
         */
        checkFirstStepCompleted() {
            return this.checkFirstStep();
        },

        /**
         * проверка, завершен ли второй шаг, что бы перейти к следующему
         * @return boolean
         */
        checkTwoStepCompleted() {
            return this.checkTwoStep();
        }
    },

    methods: {
        /**
         * Переход к первому шагу
         */
        toOneStep() {
            this.step_0 = false;
            this.step_1 = true;
            this.step_2 = false;
        },

        /**
         * Проверка всех полей на шаге #1
         * @return boolean
         */
        checkFirstStep() {
            return (this.memberName.length && this.memberPhone.length && this.memberEmail.length) ? true : false;
        },

        /**
         * Переход ко второму шагу
         */
        async toTwoStep() {
            if (!this.checkFirstStep()) {
                return;
            }

            let _params = new URLSearchParams();
            _params.append('phone', this.memberPhone);
            _params.append('email', this.memberEmail);
            let res = await this.$axios.$post('/api/auth/is-unique-data/index', _params).then((res) => {
                return res;
            }).catch((error) => {
                return {result:'error'};
            });

            if (res.result === 'success') {
                this.memberNameState = (this.memberName.length) ? true : false;
                this.memberPhoneState = res.phone;
                this.memberEmailState = res.email;

                if (res.phone && res.email) {
                    this.step_0 = false;
                    this.step_1 = false;
                    this.step_2 = true;
                }
            }
        },

        /**
         * Проверка всех полей на шаге #2
         * @return boolean
         */
        checkTwoStep() {
            return (this.companyName.length && this.companySwift.length && this.companyIban.length) ? true : false;
        },

        /**
         * Завершение регистрации
         */
        async onSubmit() {
            if (!this.checkFirstStep()) {
                return;
            }

            if (!this.checkTwoStep()) {
                return;
            }

            let _params = new URLSearchParams();
            _params.append('member_name', this.memberName);
            _params.append('member_phone', this.memberPhone);
            _params.append('member_email', this.memberEmail);
            _params.append('company_name', this.companyName);
            _params.append('company_swift', this.companySwift);
            _params.append('company_iban', this.companyIban);
            _params.append('timezone', new Date().getTimezoneOffset());

            let res = await this.$axios.$post('/api/auth/signup/index', _params).then((res) => {
                return res;
            }).catch((error) => {
                return {result:'error'};
            });

            if (res.result === 'success') {
                this.$auth.$storage.setUniversal('userName', res.name);
                this.$auth.$storage.setUniversal('userPhone', res.phone);
                this.$auth.$storage.setUniversal('userEmail', res.email);

                setTimeout(function() {
                    $nuxt.$router.push('/auth/verify-email');
                }, 200);
            } else {

            }
        },
    },
};
</script>



<style lang='scss'>

.signup-steps {
    display: table;
    width: 100%;
    height: 100%;
    margin: 1.5rem 0;
    border: 1px #000 solid;
    border-radius: 10px !important;
    border-radius: 3px;
}

._step_1{
    border-radius: 10px 0 0 10px !important;
}
._step_2{
    border-radius: 0 10px 10px 0 !important;
}

.signup-steps-item {
    position: relative;
    display: table-cell;
    width: 50%;
    padding: 10px 10px 10px 30px;
    border-left: 1px solid gray;
    color: gray;
    background-color: $light;

    &:first-child {
        border-left: 0;
    }

    @media (min-width: $grid-breakpoints-lg) {
        padding: 10px 10px 10px 50px;
    }

    &:before {
        content: '';
        position: absolute;
        top: 10px;
        left: 2px;
        width: 26px;
        height: 26px;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        opacity: 0.3;

        @media (min-width: $grid-breakpoints-lg) {
            left: 5px;
            width: 40px;
            height: 40px;
        }
    }

    &._step_1:before {
        background-image: url(~assets/svg/auth/signup-step-1.svg);
    }

    &._step_2:before {
        background-image: url(~assets/svg/auth/signup-step-2.svg);
    }

    &._active {
        color: $body-color;
        background-color: $white;

        &:before {
            opacity: 1;
        }
    }
}


.signup-step {
    display: none;

    &._active {
        display: block;
    }
}

.signup-input-info {
    margin: 0 0 1rem 0;
    color: gray;
}

.signup-step .btn{
    width: 40%;
    background: #7b797f;
    border-radius: 10px !important;
    color: #000 !important;
    text-align: center;
    transition: 0.3s;
}

.signup-step .btn:hover{
    background:  rgba(107,98,108, 0.6) !important;
    border-color: rgba(107,98,108, 0.6) !important;
    color: #000 !important;
}

.wrapper-signup .btn{
    width: 40%;
    display: inline-block;
    background: rgba(76,217,100, 1) !important;
    border-radius: 10px !important;
    color: #000 !important;
    text-align: center;
    transition: 0.3s;
}
.wrapper-signup .btn:hover{
    background:  #6ff1a1 !important;
    border-color: #6ff1a1 !important;
}

</style>
