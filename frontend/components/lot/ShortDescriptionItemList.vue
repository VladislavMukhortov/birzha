// Блок с кратким описанием объявления для списка объявлений

<template>

    <div class="order-item">
        <h4 class="order-item-title">
            <span>{{ lot.title }}</span>
        </h4>
        <hr>
        
        <p>Тип сделки - <span>{{ lot.deal }}</span></p>
        <p>Вес - <span>{{ lot.quantity }} тонн</span></p>
        <p>Цена - <span>{{ lot.price }}</span></p>
        
        <!-- <div>{{ lot.quality }}</div> -->
        <p>Год урожая - {{ lot.period }}</p>
        <!-- <div>{{ lot.basis }} | {{ lot.basis_location }}</div> -->
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
@media(min-width: 320px){
    span.wrap-list div.list-group a{
        display: block;
        margin: 1px;
        height: 300px;
    }
}
@media(min-width: 1400px){
        span.wrap-list div.list-group a{
        display: inline-block;
        width: 241px;
        margin: 1px;
        height: 300px;
    }
}
.list-group{
    display: inline-block;
    width: 100%;
}



.order-item-title {
    position: relative;
    padding: 0 150px 0 0;
    text-transform: uppercase;
}

.order-item-price {
    position: absolute;
    top: 0;
    right: 0;
}

.order-item-desc {
    position: relative;
    padding: 0 150px 0 0;
}
</style>
