<template>

    <footer class="footer">
        <b-container>
            <b-row>

                <b-col cols="12" md="4">
                    <ul class="list-unstyled">
                        <li>
                            <nuxt-link to="/" exact>Main</nuxt-link>
                        </li>
                        <li>
                            <nuxt-link to="/market">Market</nuxt-link>
                        </li>
                    </ul>
                </b-col>

                <b-col cols="12" md="4"></b-col>


                <b-col cols="12" md="4">
                    <ul class="list-unstyled">
                        <li>&copy; {{ current_year }} «Grain Market»</li>
                        <li>
                            <nuxt-link target="_blank" to="/site/offer">Contract offer</nuxt-link>
                        </li>
                        <li>
                            <nuxt-link target="_blank" to="/site/terms">Terms of Service</nuxt-link>
                        </li>
                        <li>
                            <nuxt-link target="_blank" to="/site/privacy">Privacy Statement</nuxt-link>
                        </li>
                    </ul>
                </b-col>

            </b-row>
        </b-container>
    </footer>

</template>



<script>
export default {
    data() {
        return {
            current_year: new Date().getFullYear()
        }
    }
};
</script>



<style lang='scss'>
.footer {
    padding: 1rem 0 0 0;
    background-color: $light
}
</style>
