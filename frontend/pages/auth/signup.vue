// Страница регистрации

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row>
                    <b-col cols="12" md="8">

                        <h1 class="section_title">Join Grain Market</h1>
                        <h2 class="section_subtitle">Добро пожаловать!</h2>
                        <p>Для успешной регистрации укажите информацию о себе и организации, которую представляете. Для вашего удобства, мы разбили процесс регистрации на шаги.</p>


                        <!-- STEP NUMBER -->
                        <div class="signup-steps">
                            <div class="signup-steps-item _step_1" v-bind:class="{ '_active': step_1 }">
                                <strong>Шаг 1</strong>
                                <div>Персональная информация</div>
                            </div>
                            <div class="signup-steps-item _step_2" v-bind:class="{ '_active': step_2 }">
                                <strong>Шаг 2</strong>
                                <div>Информация об организации</div>
                            </div>
                        </div>


                        <!-- STEP 0 -->
                        <div class="signup-step" v-bind:class="{ '_active': step_0 }">
                            <b-button
                                variant="primary"
                                v-on:click="toOneStep">Далее</b-button>
                        </div>


                        <!-- STEP 1 -->
                        <div class="signup-step" v-bind:class="{ '_active': step_1 }">

                            <b-form-group
                                label="Ваше имя"
                                label-class="required"
                                label-for="signup-member-name">
                                <b-input
                                    required
                                    id="signup-member-name"
                                    type="text"
                                    v-model="memberName"
                                    v-bind:state="memberNameState"></b-input>
                            </b-form-group>

                            <b-form-group
                                label="Ваш телефон"
                                label-class="required"
                                label-for="signup-member-phone">
                                <b-input
                                    required
                                    id="signup-member-phone"
                                    type="text"
                                    v-model="memberPhone"
                                    v-bind:state="memberPhoneState"></b-input>
                                <b-form-text>Номер телефона через +</b-form-text>
                                <b-form-invalid-feedback>Этот номер телефона уже занят</b-form-invalid-feedback>
                            </b-form-group>

                            <b-form-group
                                label="Email address"
                                label-class="required"
                                label-for="signup-member-email">
                                <b-input
                                    required
                                    id="signup-member-email"
                                    type="email"
                                    v-model="memberEmail"
                                    v-bind:state="memberEmailState"></b-input>
                                <b-form-invalid-feedback>Эта электронная почта уже занята</b-form-invalid-feedback>
                            </b-form-group>

                            <b-button
                                variant="primary"
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

                            <b-form-group
                                label="Company Name"
                                label-class="required"
                                label-for="signup-company-name">
                                <b-input id="signup-company-name" type="text" v-model="companyName" required></b-input>
                            </b-form-group>

                            <b-form-group
                                label="SWIFT"
                                label-class="required"
                                label-for="signup-swift">
                                <b-input id="signup-swift" type="text" v-model="companySwift" required></b-input>
                            </b-form-group>

                            <b-form-group
                                label="ACC / IBAN"
                                label-class="required"
                                label-for="signup-account">
                                <b-input id="signup-account" type="text" v-model="companyIban" required></b-input>
                            </b-form-group>

                            <hr>

                            <div class="small">By clicking «Create an account» below, you agree to our <nuxt-link target="_blank" to="/site/terms">Terms of Service</nuxt-link> and <nuxt-link target="_blank" to="/site/privacy">Privacy Statement</nuxt-link>. We'll occasionally send you account-related emails.</div>

                            <hr>

                            <b-button
                                variant="primary"
                                v-bind:class="{ 'disabled': !checkTwoStepCompleted }"
                                v-on:click="onSubmit">Create an account</b-button>

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

            if (this.memberPhone.length) {
                _params.append('member-phone', this.memberPhone);
            }

            if (this.memberEmail.length) {
                _params.append('member-email', this.memberEmail);
            }

            if (_params.toString().length) {
                let check = await this.$axios.$post('/api/auth/is-unique-data/index', _params).then((res) => {
                    return res;
                });

                this.memberNameState = (this.memberName.length) ? true : false;
                this.memberPhoneState = check.member_phone;
                this.memberEmailState = check.member_email;

                if (check.member_phone && check.member_email) {
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
            _params.append('member-name', this.memberName);
            _params.append('member-phone', this.memberPhone);
            _params.append('member-email', this.memberEmail);
            _params.append('company-name', this.companyName);
            _params.append('company-swift', this.companySwift);
            _params.append('company-iban', this.companyIban);

            let res = await this.$axios.$post('/api/auth/signup/index', _params).then((res) => {
                return res;
            });

            if (res.success) {
                this.$auth.$storage.setUniversal('userName', res.name);
                this.$auth.$storage.setUniversal('userPhone', res.phone);
                this.$auth.$storage.setUniversal('userEmail', res.email);

                setTimeout(function() {
                    $nuxt.$router.push('/auth/verify-email');
                }, 200);
            } else {

            }
        }
    }
};
</script>



<style lang='scss'>
.signup-steps {
    display: table;
    width: 100%;
    margin: 1.5rem 0;
    border: 1px solid gray;
    border-radius: 3px;
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


</style>
