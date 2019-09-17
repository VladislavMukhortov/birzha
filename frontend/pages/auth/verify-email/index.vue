
<template>

    <main>
        <section class="section_block text-center">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="8" lg="6">

                        <h1 class="section_title">Пожалуйста, подтвердите ваш адрес электронной почты</h1>
                        <p>Прежде чем вы сможете внести свой вклад, нам нужно, чтобы вы подтвердили свой адрес электронной почты.</p>
                        <p>На адрес <strong>{{ email }}</strong> было отправлено электронное письмо с инструкциями по проверке.</p>
                        <p>Не получили письмо? Отправить письмо с подтверждением.</p>

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
            title: 'Join Grain Market | site.com',
            meta: [{ hid: 'description', name: 'description', content: '' }]
        }
    },

    data() {
        return {
            email: '',
        }
    },

    mounted() {
        this.email = this.$auth.$storage.getUniversal('userEmail') || '';
    },

};
</script>



<style lang='scss'>

</style>
