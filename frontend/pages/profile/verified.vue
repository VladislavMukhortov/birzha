<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row>
                    <b-col cols="12" md="8" lg="6">

                        <h1 class="section_title">Данные контрагента</h1>


                        <b-form v-on:submit.prevent="onSubmitFormEdit">

                            <b-form-group label-for="edit-name" label="Название компании">
                                <b-input
                                    id="edit-name"
                                    v-model="companyName"
                                    v-bind:readonly="true"
                                    type="text"></b-input>
                            </b-form-group>

                            <b-form-group label-for="edit-loc" label="Адресс компании" label-class="required">
                                <b-input
                                    required
                                    id="edit-loc"
                                    v-model="companyLocation"
                                    v-bind:state="locationState"
                                    type="text"></b-input>
                                <b-form-invalid-feedback>{{ locationStateText }}</b-form-invalid-feedback>
                            </b-form-group>

                            <b-form-group label-for="edit-swift" label="SWIFT">
                                <b-input
                                    id="edit-swift"
                                    v-model="companySwift"
                                    v-bind:readonly="true"
                                    type="text"></b-input>
                            </b-form-group>

                            <b-form-group label-for="edit-iban" label="ACC / IBAN">
                                <b-input
                                    id="edit-iban"
                                    v-model="companyIban"
                                    v-bind:readonly="true"
                                    type="text"></b-input>
                            </b-form-group>

                            <b-form-group label-for="edit-bank-name" label="Название банка" label-class="required">
                                <b-input
                                    required
                                    id="edit-bank-name"
                                    v-model="bankName"
                                    v-bind:state="bankNameState"
                                    type="text"></b-input>
                                <b-form-invalid-feedback>{{ bankNameStateText }}</b-form-invalid-feedback>
                            </b-form-group>

                            <b-form-group label-for="edit-bank-loc" label="Адресс банка" label-class="required">
                                <b-input
                                    required
                                    id="edit-bank-loc"
                                    v-model="bankLocation"
                                    v-bind:state="bankLocationState"
                                    type="text"></b-input>
                                <b-form-invalid-feedback>{{ bankLocationStateText }}</b-form-invalid-feedback>
                            </b-form-group>


                            <b-form-group label-for="edit-email" label="Почтовый ящик компании" label-class="required">
                                <b-input
                                    required
                                    id="edit-email"
                                    v-model="companyEmail"
                                    v-bind:state="emailState"
                                    type="text"></b-input>
                                <b-form-invalid-feedback>{{ emailStateText }}</b-form-invalid-feedback>
                            </b-form-group>

                            <b-form-group label-for="edit-phone" label="Телефон компании" label-class="required">
                                <b-input
                                    required
                                    id="edit-phone"
                                    v-model="companyPhone"
                                    v-bind:state="phoneState"
                                    type="text"></b-input>
                                <b-form-invalid-feedback>{{ phoneStateText }}</b-form-invalid-feedback>
                            </b-form-group>

                            <b-form-group label-for="edit-site" label="Сайт компании">
                                <b-input
                                    id="edit-site"
                                    v-model="companySite"
                                    v-bind:state="siteState"
                                    type="text"></b-input>
                                <b-form-invalid-feedback>{{ siteStateText }}</b-form-invalid-feedback>
                            </b-form-group>

                            <b-form-group label-for="edit-director" label="ФИО директора компании">
                                <b-input
                                    id="edit-director"
                                    v-model="companyDirector"
                                    v-bind:state="directorState"
                                    type="text"></b-input>
                                <b-form-invalid-feedback>{{ directorStateText }}</b-form-invalid-feedback>
                            </b-form-group>

                            <b-form-group label-for="edit-text" label="Дополнительная информация">
                                <b-textarea
                                    id="edit-text"
                                    v-model="companyText"
                                    rows="3"
                                    max-rows="6"
                                    v-bind:state="textState"></b-textarea>
                                <b-form-invalid-feedback>{{ textStateText }}</b-form-invalid-feedback>
                            </b-form-group>

                            <b-button type="submit" variant="primary">Сохранить</b-button>

                        </b-form>

                    </b-col>
                </b-row>
            </b-container>
        </section>
    </main>

</template>



<script>
export default {

    head() {
        return {
            title: 'Verified Profile | site.com',
            meta: [{ hid: 'description', name: 'description', content: '' }]
        }
    },

    data() {
        return {
            companyName: '',            // название компании
            companyLocation: '',        // адресс компании
            companySwift: '',           // Society for Worldwide Interbank Financial Telecommunications
            companyIban: '',            // International Bank Account Number
            bankName: '',               // название банка
            bankLocation: '',           // адресс банка
            companyEmail: '',           // почта
            companyPhone: '',           // телефон
            companySite: '',            // сайт
            companyDirector: '',        // имя директора
            companyText: '',            // дополнительная информация

            companyVerifyEmail: false,  // статус подтверждения почты
            companyVerifyPhone: false,  // статус подтверждения телефона

            showAlertEdit: false,     // уведомление об успешном изменении данных контрагента

            locationState: '',          // состояние
            bankNameState: '',          // состояние
            bankLocationState: '',      // состояние
            emailState: '',             // состояние
            phoneState: '',             // состояние
            siteState: '',              // состояние
            directorState: '',          // состояние
            textState: '',              // состояние
            locationStateText: '',      // текст состояния
            bankNameStateText: '',      // текст состояния
            bankLocationStateText: '',  // текст состояния
            emailStateText: '',         // текст состояния
            phoneStateText: '',         // текст состояния
            siteStateText: '',          // текст состояния
            directorStateText: '',      // текст состояния
            textStateText: '',          // текст состояния
        };
    },


    async asyncData ({ $axios }) {
        let res = await $axios.$get('/api/company/my/index').then((res) => {
            return res;
        })

        return {
            companyName: res.name || '',
            companyLocation: res.location || '',
            companySwift: res.swift || '',
            companyIban: res.iban || '',
            bankName: res.bank_name || '',
            bankLocation: res.bank_location || '',
            companyEmail: res.email || '',
            companyPhone: res.phone || '',
            companySite: res.site || '',
            companyDirector: res.director || '',
            companyText: res.text || '',
            companyVerifyEmail: res.verify_email || false,
            companyVerifyPhone: res.verify_phone || false,
        };
    },


    methods: {
        /*
         *Отправка формы на изменение информации о пользователе
         */
        async onSubmitFormEdit() {
            this.showAlertEdit = false;
            this.locationState = '';
            this.bankNameState = '';
            this.bankLocationState = '';
            this.emailState = '';
            this.phoneState = '';
            this.siteState = '';
            this.directorState = '';
            this.textState = '';

            let _params = new URLSearchParams();
            _params.append('company_location', this.companyLocation);
            _params.append('bank_name', this.bankName);
            _params.append('bank_location', this.bankLocation);
            _params.append('company_email', this.companyEmail);
            _params.append('company_phone', this.companyPhone);
            _params.append('company_site', this.companySite);
            _params.append('company_director', this.companyDirector);
            _params.append('company_text', this.companyText);

            let res = await this.$axios.$post('/api/company/edit/index', _params).then((res) => {
                return res;
            });

            if (res.success) {
                this.showAlertEdit = true;
            } else {
                if (res.error) {
                    this.locationState = (res.error.company_location) ? false : true;
                    this.bankNameState = (res.error.bank_name) ? false : true;
                    this.bankLocationState = (res.error.bank_location) ? false : true;
                    this.emailState = (res.error.company_email) ? false : true;
                    this.phoneState = (res.error.company_phone) ? false : true;
                    this.siteState = (res.error.company_site) ? false : true;
                    this.directorState = (res.error.company_director) ? false : true;
                    this.textState = (res.error.company_text) ? false : true;

                    this.locationStateText = res.error.company_location || '';
                    this.bankNameStateText = res.error.bank_name || '';
                    this.bankLocationStateText = res.error.bank_location || '';
                    this.emailStateText = res.error.company_email || '';
                    this.phoneStateText = res.error.company_phone || '';
                    this.siteStateText = res.error.company_site || '';
                    this.directorStateText = res.error.company_director || '';
                    this.textStateText = res.error.company_text || '';
                }
            }
        },
    },
};
</script>



<style lang='scss'>

</style>
