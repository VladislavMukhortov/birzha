// Страница со списком категорий сделок:
// - в статусе "твердо" или ожидают "твердо"
// - на этапе переписки
// - завершенные сделки

<template>

    <main>
        <section class="section_block">
            <b-container>

                <h1 class="section_title text-center">Deals</h1>
                <p class="text-center">Выберите категорию по которой хотите просмотреть сделки</p>

                <b-row>

                    <b-col sm="4">
                        <p>Сделки которые ожидают "твердо" и которые имеют статус "твердо"</p>
                        <p>
                            <b-link class="btn btn-primary btn-block" to="/deals/auction">auction</b-link>
                        </p>
                    </b-col>

                    <b-col sm="4">
                        <p>Сделки которые находятся на этапе общения</p>
                        <p>
                            <b-link class="btn btn-primary btn-block" to="/deals/communication">communication</b-link>
                        </p>
                    </b-col>

                    <b-col sm="4">
                        <p>Завершенные сделки</p>
                        <p>
                            <b-link class="btn btn-primary btn-block" to="/deals/archive">archive</b-link>
                        </p>
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
            title: 'Deals | site.com',
            meta: [{ hid: 'description', name: 'description', content: '' }]
        }
    },
};
</script>



<style lang='scss'>
</style>
