// Страница для 404 ошибки
// сюда редиректит если объявление не найденно

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="10" lg="8">

                        <h1 class="section_title text-center">Lot not found</h1>

                        <p class="text-center">
                            <nuxt-link to="/market">TO Back</nuxt-link>
                        </p>

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
            title: 'Lot not found | site.com',
            meta: [
                { hid: 'description', name: 'description', content: '' }
            ]
        }
    },
};
</script>



<style lang='scss'>
</style>
