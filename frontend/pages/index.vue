<template>

    <main>
        <section class="section_block">
            <b-container class="slider-xxx">
                <h1 class="h4 text-center mb-4">Grain Market</h1>
                
                <!-- <div>User status: <span>{{ $auth.loggedIn }}</span></div>
                <div>User status: <span>{{ $auth.loggedIn ? 'Авторизован' : 'не Авторизован' }}</span></div>
                <div>User status: <span>{{ $auth.$state.loggedIn }}</span></div>
                <div>User status: <span>{{ $auth.$state.loggedIn ? 'Авторизован' : 'не Авторизован' }}</span></div> -->
                <div class="slider">
                    <div class="slider-wrapper">
                        <div class="slider-item"
                            v-for="item in slideItems"
                            :key="item.id"
                        >
                        <router-link v-bind:to="{ path: '/market'}">
                            <div class="slider-title">
                                <p>хотите купить или продать культуру?</p>
                            </div>
                        </router-link>
                        
                            <img class="slider-img" 
                                :src=" item.sourse "
                                :style="{ 'margin-left': -100 * defaultIndex + '%'}"
                                >
                        </div>
                    </div> 
                    <button class="prev-btn btn btn-primary" v-on:click="prevSlide"><</button>
                    <button class="next-btn btn btn-primary" v-on:click="nextSlide">></button>
                </div>

                <div class="text-block">
                    Мировая фьючерсная индустрия когда-то началась именно с зерна. В то время фермеры, обеспокоенные нестабильностью рынка, пытались таким образом зафиксировать цену продажи будущего урожая. Рынок наличного зерна в России живет своей жизнью, а биржевой фьючерсный рынок — своей. И все попытки как-то скрестить их до сих пор, к сожалению, особым успехом не увенчались. Тому есть много причин, главная из которых — неготовность основных игроков рынка реального товара к использованию биржевых инструментов в повседневной деятельности.
                    <img class="text-block-img" src="img/text-block1.jpg">
                    Мы предлагаем к вашему вниманию, современную биржу культур, где люди со всего мира смогут покупать и продавать культуры по взаимовыгодным ценам. Наша платформа создаёт возможность проводить выгодные и безопасные сделки, объединяя участников из всех отраслей сельсхозяйственной деятельности. У всех участников рынка — равные возможности для продажи-покупки культур. Закупочные цены для всех одинаковые, так как фермер и трейдер дают предложения напрямую.
                    <img class="text-block-img" src="img/text-block2.jpg">
                    Фермеры и трейдеры создают объявления о покупке или о продаже хозяйственных культур, так же отбирают предложения по фильтру, согласно указанным параметрам, объему и местоположению товара. Логистические и транспортные компании принимают заявки на перевозку культур. Работайте так, как привыкли. Мы не меняем бизнес-процессы рынка и не вмешиваемся в процесс сделки. Все участники рынка договариваются между собой напрямую. Для быстрой и удобной связи, мы создали чат в личном кабинете, где можно обмениваться сообщениями и документами.
                    <img class="text-block-img" src="img/text-block3.jpg">
                    <h3 style="text-align: center">Вы — Фермер</h3>
                    <ul class="text-list">
                        <li>Принимайте предложения от трейдеров, элеваторов и перерабатывающих заводов на покупку</li>
                        <li>Откликайтесь на заявки трейдеров и перерабатывающих заводов о покупке зерна</li>
                        <li>Смотрите тарифы элеваторов, терминалов, перерабатывающих заводов и пунктов перехода</li>
                        <li>Обсуждайте детали сделки и обменивайтесь документами с контрагентами в чате</li>
                        <li>Смотрите проверенные отзывы о работе контрагента и выставляйте свой рейтинг</li>
                    </ul>
                    <img class="text-block-img" src="img/text-block4.jpg">
                    <h3 style="text-align: center">Вы — Трейдер</h3>
                    <ul class="text-list">
                        <li>Создавайте объявления на покупку продукции у фермеров со своими условиями и ценами</li>
                        <li>Откликайтесь на предложения фермеров</li>
                        <li>Смотрите тарифы элеваторов, терминалов, перерабатывающих заводов и пунктов перехода</li>
                        <li>Обсуждайте детали сделки и обменивайтесь документами с контрагентами в чате</li>
                        <li>Смотрите проверенные отзывы о работе контрагента и выставляйте свой рейтинг</li>
                    </ul>
                    <img class="text-block-img" src="img/text-block5.jpg">
                    <h3 style="text-align: center">Что бы полноценно пользоваться сайтом Нужно:</h3>
                    <router-link class="main-text-link" v-bind:to="{ path: '/auth/signin'}">Войти</router-link>
                    <router-link class="main-text-link" v-bind:to="{ path: '/auth/signup'}">Зарегистрироваться</router-link>
                </div>
                
            </b-container>
        </section>
    </main>

</template>



<script>
export default {
    auth: false,
    data(){
        return {
            defaultIndex: 0,
            slideItems: [
                {id: 1, sourse: "img/slider1.jpg"},
                {id: 2, sourse: "img/slider2.jpg"},
                {id: 3, sourse: "img/slider3.jpg"},
                {id: 4, sourse: "img/slider4.jpg"},
            ]
        }
    },
    methods: {
        prevSlide(){
            if(this.defaultIndex != 0){
                this.defaultIndex--;
            }
            else{
                this.defaultIndex = 3
            }
            
            console.log();
        },
        nextSlide(){
            if(this.defaultIndex >= this.slideItems.length - 1){
                this.defaultIndex = 0
            }
            else{
                this.defaultIndex++;
            }
            
        }
    },
    mounted() {
        let vm = this;
        setInterval(function(){
            vm.nextSlide();
        }, 5000);
    }
};
</script>



<style lang='scss'>
    .slider-xxx{
        width: 60%;
    }
    .slider{
        position: relative;
        overflow: hidden;
        width: 100%;
        transition: left 0.6s ease-in-out;
    }
    .slider a{
        color: #000;
    }
    .slider-wrapper{
        width: 100%;
        display: flex;
    }
    .slider-item{
        flex: 0 0 100%;
        max-width: 100%;
    }
    .slider-title{
        position: absolute;
        width: 100%;
        heigth: 50px;
        background: rgba(165, 149, 149, 0.65);
    }
    .slider-title:hover{
        background: rgba(165, 149, 149, 0.4);
        cursor: pointer;
    }
    .slider-title p{
        text-align: center;
        padding-top: 10px;
        font-size: 25px;
        
    }
    .slider-title p:hover{
        opacity: 0.8;
    }
    .slider-img{
        height: 300px;
        width: 100%;
        -webkit-transition: .5s ease-in-out;
        -moz-transition: .5s ease-in-out;
        -o-transition: .5s ease-in-out;
        transition: .5s ease-in-out;
    }
    .prev-btn{
        position: absolute;
        top: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        color: #fff;
        text-align: center;
        opacity: 0.5;
        height: 50px;
        transform: translateY(-50%);
        left: 0;
    }
    .next-btn{
        position: absolute;
        top: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        color: #fff;
        text-align: center;
        opacity: 0.5;
        height: 50px;
        transform: translateY(-50%);
        right: 0;
    }
    .text-block{
        margin-top: 50px;
        border: 1px solid #000;
        border-radius: 10px;
        padding: 20px;
    }
    .text-block-img{
        width: 100%;
        height: 400px;
        margin-top: 35px;
        margin-bottom: 35px;
    }
    .text-list li{
        margin-top: 15px;
        margin-bottom: 15px;
    }
    .main-text-link{
    width: 49.7%;
    height: 40px;
    font-size: 25px;
    display: inline-block;
    background: rgba(123,121,127, 1) !important;
    border-radius: 10px !important;
    border: 1px #000 solid !important;
    color: #000 !important;
    text-align: center;
    transition: 0.3s;
    }
    .main-text-link:hover{
        background:  rgba(107,98,108, 0.6) !important;
        border-color: rgba(107,98,108, 0.6) !important;
    }
    @media(min-width: 320px) and(max-width: 700px){
        .slider-xxx{
            width: 100%;
        }
        .slider{
            position: relative;
            overflow: hidden;
            width: 100%;
            transition: left 0.6s ease-in-out;
        }
        .slider a{
            color: #000;
        }
        .slider-wrapper{
            width: 100%;
            display: flex;
        }
        .slider-item{
            flex: 0 0 100%;
            max-width: 100%;
        }
        .slider-title{
            position: absolute;
            width: 100%;
            heigth: 50px;
            background: rgba(165, 149, 149, 0.65);
        }
        .slider-title:hover{
            background: rgba(165, 149, 149, 0.4);
            cursor: pointer;
        }
        .slider-title p{
            text-align: center;
            padding-top: 10px;
            font-size: 15px;
            
        }
        .slider-title p:hover{
            opacity: 0.8;
        }
        .slider-img{
            height: 150px;
            width: 100%;
            -webkit-transition: .5s ease-in-out;
            -moz-transition: .5s ease-in-out;
            -o-transition: .5s ease-in-out;
            transition: .5s ease-in-out;
        }
        .prev-btn{
            position: absolute;
            top: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            color: #fff;
            text-align: center;
            opacity: 0.5;
            height: 25px;
            transform: translateY(-50%);
            left: 0;
        }
        .next-btn{
            position: absolute;
            top: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            color: #fff;
            text-align: center;
            opacity: 0.5;
            height: 25px;
            transform: translateY(-50%);
            right: 0;
        }
        .text-block{
            margin-top: 50px;
            border: 1px solid #000;
            border-radius: 10px;
            padding: 20px;
        }
        .text-block-img{
            width: 100%;
            height: 200px;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .text-list li{
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .main-text-link{
        width: 100%;
        height: 30px;
        margin-top: 5px;
        margin-bottom: 5px;
        font-size: 20px;
        display: inline-block;
        background: rgba(123,121,127, 1) !important;
        border-radius: 10px !important;
        border: 1px #000 solid !important;
        color: #000 !important;
        text-align: center;
        transition: 0.3s;
        }
        .main-text-link:hover{
            background:  rgba(107,98,108, 0.6) !important;
            border-color: rgba(107,98,108, 0.6) !important;
        }
    }
    @media(min-width: 768px) and (max-width: 1025px){
        .slider-xxx{
            width: 70%;
        }
        .main-text-link{
        width: 97%;
        height: 30px;
        margin-top: 5px;
        margin-bottom: 5px;
        font-size: 20px;
        display: inline-block;
        background: rgba(123,121,127, 1) !important;
        border-radius: 10px !important;
        border: 1px #000 solid !important;
        color: #000 !important;
        text-align: center;
        transition: 0.3s;
        }
    }
    
</style>
