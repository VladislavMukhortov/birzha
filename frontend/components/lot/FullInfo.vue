// Блок с описанием объявления для списка объявлений

<template>

    <div>
        <div class="wrap-info">
            <h1 class="section_title">{{ lot.title }}</h1>

            <div>Тип: <b>{{ lot.deal }}</b></div>
            <div>Цена: <b>{{ lot.price }}</b></div>
            <div>Объем: <b>{{ lot.quantity }} тонн</b></div>
            <div>Период поставки: <b>{{ lot.period }}</b></div>
            <div>Базис: <b>{{ lot.basis }}</b></div>
            <div>Базис поставки: <b>{{ lot.basis_location }}</b></div>
        </div>
        
        <div class="wrap-quality">
            <div>Основные параметры:</div>
            <ul v-if="lot.quality.length" class="list-unstyled">
                <li v-for="(item, index) in lot.quality" v-bind:key="index">{{ item.name }}: <b>{{ item.val }}</b></li>
            </ul>
        </div>
        <template v-if="lot.text">
            <div class="wrap-additional-info">
                <b>Дополнительная информация:</b>
                <p>{{ lot.text }}</p>
            </div>
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
@media(max-width: 375px){
    .wrap-info{
        border: 1px #d0c8d0 solid;
        border-radius: 10px;
        padding: 10px;
        width:100%;
        margin-top: 5px;
        float: left;
        margin-right: 0.5px;
    }
    .wrap-quality{
        border: 1px #d0c8d0 solid;
        border-radius: 10px;
        padding: 10px;
        width:100%;
        margin-top: 5px;
        float: right;
        margin-left: 0.5px;
    }
}
@media(min-width: 425px){
    .wrap-info{
        border: 1px #d0c8d0 solid;
        border-radius: 10px;
        padding: 10px;
        width:49.5%;
        margin-top: 5px;
        float: left;
        margin-right: 0.5px;
        min-height: 310px;
    }
    .wrap-quality{
        border: 1px #d0c8d0 solid;
        border-radius: 10px;
        padding: 10px;
        width:49.5%;
        margin-top: 5px;
        float: right;
        margin-left: 0.5px;
        min-height: 310px;
    }
}

.wrap-bot-div{
    border: 1px #d0c8d0 solid;
    margin-top: 5px;
    border-radius: 10px;
    padding: 10px;
    background: #f4f5f7;
    float: right;
}
.wrap-additional-info{
    border: 1px #d0c8d0 solid;
    border-radius: 10px;
    padding: 10px;
    width:49.5%;
    margin-top: 5px;
    float: left;
    width: 100%;
    margin-left: 0.5px;
    
}
</style>
