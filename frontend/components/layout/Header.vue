<template>

    <header class="header">
        <b-navbar toggleable="md" type="light" class="shadow-sm">
            <b-container>

                <b-navbar-brand to="/" exact>Grain Market</b-navbar-brand>

                <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

                <b-collapse id="nav-collapse" class="header-nav-collapse" is-nav>


                    <template v-if="$auth.loggedIn">
                        <b-navbar-nav>
                            <b-nav-item to="/" exact>Main</b-nav-item>
                            <b-nav-item to="/market">Market</b-nav-item>
                            <b-nav-item to="/orders" exact>My orders</b-nav-item>
                            <b-nav-item to="/orders/create">Create</b-nav-item>
                            <b-nav-item to="/deals">Сделки</b-nav-item>
                            <b-nav-item to="/profile" exact>Profile</b-nav-item>
                        </b-navbar-nav>
                        <b-navbar-nav class="__right">
                            <li class="nav-item">
                                <b-button class="nav-link" variant="primary" v-on:click="$auth.logout()">Logout</b-button>
                            </li>
                        </b-navbar-nav>
                    </template>
                    <template v-else>
                        <b-navbar-nav class="__right">
                            <b-nav-item to="/" exact>Main</b-nav-item>
                            <b-nav-item to="/market">Market</b-nav-item>
                            <b-nav-item to="/auth/signin">Signin</b-nav-item>
                            <b-nav-item link-classes="btn btn-primary" to="/auth/signup">Signup</b-nav-item>
                        </b-navbar-nav>
                    </template>

                </b-collapse>

            </b-container>
        </b-navbar>
    </header>

</template>



<script>
export default {};
</script>



<style lang='scss'>
.header {

}

.header-nav-collapse {
    @media (min-width: $grid-breakpoints-lg) {
        margin: 0 0 0 3rem;
    }
}

.navbar-nav {
    &.__right {
        margin: 0 0 0 auto;
    }
}
</style>
