<template>

    <div class="order-item">
        <h4 class="order-item-title">
            <span>{{ lot.title }}</span>
            <span class="order-item-price">{{ lot.price }}</span>
        </h4>
        <div class="order-item-desc">
            <span>{{ lot.deal }}</span>
            <span class="order-item-price">{{ lot.quantity }} тонн</span>
        </div>
        <div>{{ lot.quality }}</div>
        <div>{{ lot.period }}</div>
        <div>{{ lot.basis }} | {{ lot.basis_location }}</div>
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
.order-item {

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
