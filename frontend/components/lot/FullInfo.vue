// Блок с описанием объявления для списка объявлений

<template>

    <div>
        <h1 class="section_title">{{ lot.title }}</h1>

        <div>Тип: {{ lot.deal }}</div>
        <div>Цена: {{ lot.price }}</div>
        <div>Объем: {{ lot.quantity }} тонн</div>
        <div>Период поставки: {{ lot.period }}</div>
        <div>Базис: {{ lot.basis }}</div>
        <div>Базис поставки: {{ lot.basis_location }}</div>

        <hr>

        <div>Основные параметры:</div>
        <ul v-if="lot.quality.length" class="list-unstyled">
            <li v-for="(item, index) in lot.quality" v-bind:key="index">{{ item.name }}: {{ item.val }}</li>
        </ul>

        <template v-if="lot.text">
            <hr>
            <div>Дополнительная информация:</div>
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
