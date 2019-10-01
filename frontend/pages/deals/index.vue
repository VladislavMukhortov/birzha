// Страница со списком сделок:
// - ожидающих "твердо"
// - "твердо"
// - на этапе общения
// - завершеные

<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="8" lg="8">

                        <h1 class="section_title text-center">Deals</h1>

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
            title: 'Deals | site.com',
            meta: [{ hid: 'description', name: 'description', content: '' }]
        }
    },
};
</script>



<style lang='scss'>
.market-content {
    margin: 1rem 0 0 0;
}
</style>
