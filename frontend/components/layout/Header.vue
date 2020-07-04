<template>

    <header class="header ">
        <b-navbar toggleable="md" type="light" class="shadow-sm">
            <b-container>

                <b-navbar-brand to="/" exact><span class="grain-logo"><b>Grain Market</b></span></b-navbar-brand>

                <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

                <b-collapse id="nav-collapse" class="header-nav-collapse" is-nav>


                    <template v-if="$auth.loggedIn">
                        <b-navbar-nav>
                            <div class="span-item" @click="activeItem">
                                <b-nav-item to="/" exact>Main</b-nav-item>
                            </div>
                            <div class="span-item">
                                <b-nav-item to="/market">Market</b-nav-item>
                            </div>
                            <div class="span-item">
                                <b-nav-item to="/orders" exact>My orders</b-nav-item>
                            </div>
                            <div class="span-item">
                                <b-nav-item to="/orders/create">Create</b-nav-item>
                            </div>
                            <div class="span-item" >
                                <b-nav-item to="/deals">Сделки</b-nav-item>
                            </div>
                            <div class="span-item">
                                <b-nav-item to="/profile" exact>Profile</b-nav-item>
                            </div>
                        </b-navbar-nav>
                        <b-navbar-nav class="__right">
                            <li class="nav-item">
                                <b-button class="nav-link log-btn" v-on:click="$auth.logout(); redirectLogout();">Logout</b-button>
                            </li>
                        </b-navbar-nav>
                    </template>
                    <template v-else>
                        <b-navbar-nav class="__right">
                            <div class="span-item">
                                <b-nav-item to="/" exact>Main</b-nav-item>
                            </div>
                            <div class="span-item">
                                <b-nav-item to="/market">Market</b-nav-item>
                            </div>
                            <div class="span-item">
                                <b-nav-item to="/auth/signin">Signin</b-nav-item>
                            </div>
                                <b-nav-item link-classes="btn log-btn" to="/auth/signup">Signup</b-nav-item>
                        </b-navbar-nav>
                    </template>

                </b-collapse>

            </b-container>
        </b-navbar>
        <pre></pre>
    </header>

</template>



<script>
export default {
    data(){
        return{
            active: true
        }
    },
    methods: {
        redirectLogout: function(){
            window.location.href = "/"
        }
    }
};

</script>



<style lang='scss'>
@media(min-width: 426px) and (max-width: 1000px){
    div.span-item a.nav-link{
        color: #131415 !important;
        transition: 0.3s;
        font-size: 15px;
    }

    div.span-item a.nav-link:hover{
        background:  rgba(107,98,108, 0.6);
        border-radius: 10px;
        font-size: 15px !important;
        height: 100%;
    }

    div.span-item a.nuxt-link-active{
        background:  rgba(107,96,96, 0.7);
        color: #000;
        border-radius: 10px;
        height: 100%;
        font-size: 15px !important;
        margin-right: 10px;
        margin-left: 10px;

    }
}
.header {
    min-height: 242px;
    background-image: url(https://www.irmeteo.ru/assets/images/news/july-17/wheat-big2.jpg);
    box-shadow: 0em 0.4em 5px rgba(122,122,122,0.5);
}

.shadow-sm{
    background-color: rgba(185,179,179, 0.5);
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

.grain-logo{
    font-size: 30px;
}

div.span-item a.nav-link{
    color: #131415 !important;
    transition: 0.3s;
}

div.span-item a.nav-link:hover{
    background:  rgba(107,98,108, 0.6);
    border-radius: 10px;
    font-size: 20px;
    height: 100%;
}

div.span-item a.nuxt-link-active{
    background:  rgba(107,96,96, 0.7);
    color: #000;
    border-radius: 10px;
    height: 100%;
    font-size: 20px;
    margin-right: 10px;
    margin-left: 10px;

}

.log-btn{
    background:  rgba(107,96,96, 0.7);
    border-radius: 10px;
    font-size: 20px;
    height: 100%;
    color: #000 !important;
    border: none;
    transition: 0.3s;
}

.log-btn:hover{
    background:  rgba(107,98,108, 0.6);
    border-radius: 10px;
    font-size: 20px;
    height: 100%;
    color: #000 !important;
}


</style>
