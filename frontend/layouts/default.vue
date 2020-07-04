<template>

    <div class="site-page">

        <Header />
        <div class="main-site-controller">
            <nuxt />
        </div>
        <Footer />

    </div>

</template>



<script>
import Header from '~/components/layout/Header.vue';
import Footer from '~/components/layout/Footer.vue';

export default {
    components: {
        Header,
        Footer
    }
};
</script>



<style lang='scss'>
.main-site-controller{
    min-height: 800px;
}
</style>
