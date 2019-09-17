<template>

    <main>
        <section class="section_block">
            <b-container>
                <b-row class="justify-content-center">
                    <b-col cols="12" md="8" lg="6">

                        <h1 class="h4 text-center mb-4">Grain Market</h1>

                        <div>User status: <span>{{ $auth.loggedIn }}</span></div>
                        <div>User status: <span>{{ $auth.loggedIn ? 'Авторизован' : 'не Авторизован' }}</span></div>
                        <div>User status: <span>{{ $auth.$state.loggedIn }}</span></div>
                        <div>User status: <span>{{ $auth.$state.loggedIn ? 'Авторизован' : 'не Авторизован' }}</span></div>

                    </b-col>
                </b-row>
            </b-container>
        </section>
    </main>

</template>



<script>
export default {
    auth: false
};
</script>



<style lang='scss'>

</style>
