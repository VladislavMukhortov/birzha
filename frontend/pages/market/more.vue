<template>
    <main>
        <div class="container-more">
            <div class="wrapper-more">
                <div class="wrapper-img">
                    <img v-bind:src="info.image" class="more-image">
                </div>
                    
                    <div class="more-desc">
                        <h3 class="title-more">
                            {{info.name}}
                        </h3>
                        <br>
                        {{info.description}}
                        <br>
                        <router-link v-bind:to="{ path: '/market'}">
                            {{back}}
                        </router-link>
                    </div>
                    
                </div>
            </div>
    </main>
</template>

<script>
export default {
    auth: false,

    head() {
        return {
            title: 'Market crops | site.com',
            meta: [{ hid: 'description', name: 'description', content: '' }],
        }
    },

    data() {
        return {
            route: this.$route.query.id,
            back: "<<Назад"
        }
    },

    async asyncData ({ $axios, route}) {

        let getParams = {params:{
                id: route.query.id
            }
        }

        let res = await $axios.$get('/api/crop/list/info', getParams).then((res) => {
            return res;
        }).catch((error) => {
            return {};
        });
        console.log(res);

        return {info: res}
        
    }
};
</script>

<style lang='scss'>
@media (min-width: 320px){
    .container-more{
        margin: 0 auto;
        width: 95%;
    }

    .wrapper-more{
        margin-top: 30px;
    }

    .more-image{
        border-radius: 10px;
        height: 400px;
        width: 100%;
    }

    .title-more{
        text-align: center;
    }

    .wrapper-desc{
        border: 1px solid grey;
        border-radius: 10px;
        width: 100%;
        margin-top: 10px;
        margin-bottom: 10px;
        padding: 10px;
    }
}

@media (min-width: 1024px){
    .container-more{
        margin: 0 auto;
        width: 80%;
    }

    .wrapper-more{
        margin-top: 100px;
    }

    .more-image{
        float: left;
        border-radius: 10px;
        height: 40%;
        width: 40%;
    }

    .title-more{
        text-align: center;
    }

    .more-desc{
        border: 1px solid grey;
        border-radius: 10px;
        float: right;
        width: 55%;
        padding: 10px;
    }

}

</style>
