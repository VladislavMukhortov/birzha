// Блок с описанием объявления для списка объявлений

<template>

    <div>
        <h1 class="section_title">{{ lot.title }}</h1>

        <div>Тип: <b>{{ lot.deal }}</b></div>
        <div>Цена: <b>{{ lot.price }}</b></div>
        <div>Объем: <b>{{ lot.quantity }} тонн</b></div>
        <div>Период поставки: <b>{{ lot.period }}</b></div>
        <div>Базис: <b>{{ lot.basis }}</b></div>
        <div>Базис поставки: <b>{{ lot.basis_location }}</b></div>

        <hr>

        <div>Основные параметры:</div>
        <ul v-if="lot.quality.length" class="list-unstyled">
            <li v-for="(item, index) in lot.quality" v-bind:key="index">{{ item.name }}: <b>{{ item.val }}</b></li>
        </ul>

        <template v-if="lot.text">
            <hr>
            <div><b>Дополнительная информация:</b></div>
            <p>{{ lot.text }}</p>
        </template>
    </div>

</template>



<script>
export default {
    props: {
        lot: {
            type: Object
        },
    },
};
</script>



<style lang='scss'>
</style>
