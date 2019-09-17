<template>

    <main></main>

</template>



<script>
export default {
    auth: false,

    head() {
        return {
            title: 'Verify email | site.com',
        }
    },

    data() {
        return {
            success: null,
            company: '',
            name: '',
            phone: '',
            email: '',
            access_token: ''
        }
    },

    async asyncData({ $axios, params }) {
        let _params = {
            params: {
                token: params.token
            }
        };

        let res = await $axios.$get('/api/auth/verify-email/index', _params).then((res) => {
            return res;
        });

        return {
            success: res.success,
            company: res.company,
            name: res.name,
            phone: res.phone,
            email: res.email,
            access_token: res.access_token
        };
    },

    mounted() {
        if (this.success) {
            this.$auth.$storage.setUniversal('companyName', this.company);
            this.$auth.$storage.setUniversal('userName', this.name);
            this.$auth.$storage.setUniversal('userPhone', this.phone);
            this.$auth.$storage.setUniversal('userEmail', this.email);

            this.$auth.setUserToken(this.access_token);

            $nuxt.$router.push('/profile');
        } else {
            $nuxt.$router.push('/market');
        }
    }
};
</script>



<style lang='scss'>

</style>
