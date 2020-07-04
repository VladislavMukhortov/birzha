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

                    <div class="deals-btn-wrapper">
                        <p>Сделки которые ожидают "твердо" и которые имеют статус "твердо"</p>
                        <p>
                            <b-link class="btn btn-block" to="/deals/auction">auction</b-link>
                        </p>
                    </div>

                    <div class="deals-btn-wrapper">
                        <p>Сделки которые находятся на этапе общения</p><br>
                        <p>
                            <b-link class="btn btn-block" to="/deals/communication">communication</b-link>
                        </p>
                    </div>

                    <div class="deals-btn-wrapper">
                        <p>Завершенные сделки</p><br>
                        <p>
                            <b-link class="btn btn-block" to="/deals/archive">archive</b-link>
                        </p>
                    </div>

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
.deals-btn-wrapper{
    border-radius: 10px !important;
    border: 1px #000 solid !important;
    margin: 2px;
    padding: 5px;
    width: 32.9% !important;
    min-height: 200px;
    align-items: center;
}
.deals-btn-wrapper .btn{
    margin-top: 20%;
    margin-left: 5%;
    text-align: center;
    width: 90%;
    display: inline-block;
    background: rgba(123,121,127, 1) !important;
    border-radius: 10px !important;
    border: 1px #000 solid !important;
    color: #000 !important;
    text-align: center;
    transition: 0.3s;
}
.deals-btn-wrapper .btn:hover{
    background:  rgba(107,98,108, 0.6) !important;
    border-color: rgba(107,98,108, 0.6) !important;
}
</style>
