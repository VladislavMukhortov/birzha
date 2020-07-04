
<template>
    <footer class="footer">
        <button class="support btn" v-on:click="visible = true">Написать в поддержку</button>
        <div class="modal-container" v-if="visible">
            <div class="container-close" v-on:click="visible = false"></div>
            <div class="modal">
                <div class="container-close-btn">
                    <div class="wrapper-close-btn" v-on:click="visible = false">
                        <span class="close-modal">X</span>
                    </div> 
                </div>
                <div class="form-wrapper-modal">
                    <form class="form-modal" method="post">
                        <h5 class="modal-title">Обращение в поддержку</h5>
                        <label for="modal-name" class="label-modal" v-if="currentName">Ваше имя</label>
                        <label for="modal-name" class="label-modal" v-else style="color: red">Имя не менее 2 символов</label>
                        <input name="modal-name" class="input-modal" v-model="modelParams.name">
                        <label for="modal-email" class="label-modal" v-if="currentEmail">Ваш email</label>
                        <label for="modal-email" class="label-modal" v-else style="color: red">Введите корректный email</label>
                        <input name="modal-email" class="input-modal" v-model="modelParams.email">
                        <label for="modal-text" class="label-modal" v-if="currentText">Текст обращения</label>
                        <label for="modal-name" class="label-modal" v-else style="color: red">Текст не менее 10 символов</label>
                        <textarea name="modal-text" class="text-modal" v-model="modelParams.text"></textarea>
                        <button class="btn btn-modal" return="false" v-on:click="sendModel">Отправить</button>
                    </form>
                </div>
                </div>
            </div>   
        </div>

        <div class="modal-container" v-if="successSend">
            <div class="container-close" v-on:click="successSend = false"></div>
            <div class="modal-success">
                <div class="container-close-btn">
                    <div class="wrapper-close-btn" v-on:click="successSend = false">
                        <span class="close-modal">X</span>
                    </div> 
                </div>
                    <div class="container-success">
                        <h3 class="success-title">Сообщение успешно отправлено</h3>
                        <button class="btn btn-modal btn-modal-success" return="false" v-on:click="successSend = false">Закрыть</button>
                    </div>
                </div>
            </div>   
        </div>

        <b-container class="container-footer">
            <h2 class="logo-footer">Grain Market</h2>
            <b-row class="row-footer">
                
                <b-col cols="12" md="4">
                    <ul class="list-unstyled">
                        <nuxt-link to="/" exact><li class="item-footer">Main</li></nuxt-link>
                        
                        <nuxt-link to="/market"><li class="item-footer">Market</li></nuxt-link>
                    
                        <nuxt-link to="/auth/signin"><li class="item-footer">Signin</li></nuxt-link>
                    </ul>
                </b-col>

                <b-col cols="12" md="4"></b-col>


                <b-col cols="12" md="4">
                    <ul class="list-unstyled">                       
                        <nuxt-link target="_blank" to="/site/offer"><li class="item-footer">Contract offer</li></nuxt-link>
                    
                        <nuxt-link target="_blank" to="/site/terms"><li class="item-footer">Terms of Service</li></nuxt-link>
                    
                        <nuxt-link target="_blank" to="/site/privacy"><li class="item-footer">Privacy Statement</li></nuxt-link> 
                    </ul>
                </b-col>


            </b-row>
            <ul class="list-unstyled year-footer"> 
                <li>&copy; {{ current_year }} «Grain Market»</li>
            </ul>
        </b-container>
    </footer>

</template>



<script>
export default {
    data() {
        return {
            visible: false,
            successSend: false,
            currentName: true,
            currentEmail: true,
            currentText: true,
            current_year: new Date().getFullYear(),
            modelParams: {
                name: '',
                email: '',
                text: ''
            }
        }
    },
    methods: {
        async sendModel(event) {
            event.preventDefault();
            let modelParams = {
                name: this.modelParams.name,
                email: this.modelParams.email,
                text: this.modelParams.text,
            };
            let pattern = /\S+@\S+\.\S+/;
            if(modelParams.name.length < 2){
                this.currentName = false;
            }
            else{
                this.currentName = true;
            }
            if(!pattern.test(modelParams.email)){
                this.currentEmail = false;
            }
            else{
                this.currentEmail = true;
            }
            if(modelParams.text.length < 10){
                this.currentText = false;
            }
            else{
                this.currentText = true;
            }
            if(this.currentName && this.currentEmail && this.currentText){
                let res = await this.$axios.$post('/api/models/support', modelParams).then((res) => {
                    return res;
                });
                this.successSend = true;
                this.visible = false;
            }
        },
    }
};
</script>



<style lang='scss'>
.btn{
    cursor: pointer;
}
.support:hover{
    background:  rgba(107,98,108, 0.6);
}
.modal-container{
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.3);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2;
}
.modal {
    background: #FFFFFF;
    box-shadow: 2px 2px 20px 1px;
    overflow-x: auto;
    display: flex;
    flex-direction: column;
    position: relative;
    width: 600px;
    height: 400px;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 3;
}
.container-close{
    width: 100%;
    height: 100%;
    position: absolute;
    
}
.container-close-btn{
    display: flex;
    justify-content: flex-end;
}
.wrapper-close-btn{
    display: flex;
    justify-content: center;
    width: 50px;
    height: 40px;
    background: red;
    text-align: center;
    cursor: pointer;
}
.wrapper-close-btn:hover{
    background: #ff8383;
}
.close-modal{ 
    font-size: 26px;
    text-align: center;
    cursor: pointer;
}
.close-modal:hover{
    opacity: 0.7;
}
.form-wrapper-modal{
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-title{
    text-align: center;
    margin-bottom: 20px;
}
.label-modal{
    display: flex;
    width: 100%;
    justify-content: center;
}
.input-modal{
    width: 100%;
    border-radius: 10px;
    text-align: center;
    outline: 0;
    outline-offset: 0;
}
.text-modal{
    width: 100%;
    border-radius: 10px;
    outline: 0;
    outline-offset: 0;
}
.btn-modal{
    width: 100%;
    margin-top: 20px;
    margin-bottom: 10px;
    text-align: center;
    border-radius: 10px;
    background: rgba(33,37,41, 0.7);
    font-size: 20px;
    transition: 0.3s;
}
.btn-modal:hover{
    background:  rgba(107,98,108, 0.6);
}
.modal-success{
    background: #FFFFFF;
    box-shadow: 2px 2px 20px 1px;
    overflow-x: auto;
    display: flex;
    flex-flow: column wrap;
    position: relative;
    width: 600px;
    height: 200px;
    z-index: 3;
}
.container-success{
    margin: 0 auto;
    width: 80%;
}
.success-title{
    text-align: center;
}
.btn-modal-success{
    width: 100%;
    justify-contant: center;
}
div.this .nav-link{
    width: 99%;
    display: inline-block;
    background: white !important;
    border-radius: 10px !important;
    color: #000 !important;
    border: 1px #000 solid !important;
    text-align: center;
    transition: 0.3s;
    margin: 2.5px;
}
div.this li.nav-item .active{
    width: 99%;
    display: inline-block;
    background: rgba(123,121,127, 1) !important;
    border-radius: 10px !important;
    border: 1px #000 solid !important;
    color: #000 !important;
    text-align: center;
    transition: 0.3s;
}
div.this .nav-link:hover{
    background:  rgba(107,98,108, 0.6) !important;
    border-color: rgba(107,98,108, 0.6) !important;
}
.btn:active{
    opacity: 0.3;
}
.item-footer{
    color: #000;
}
@media(min-width: 320px) and (max-width: 370px){
    .container-success{
        margin: 0 auto;
        width: 100%;
    }
}
@media(min-width: 320px){
    .footer {
        padding: 1rem 0 0 0;
        margin: 0;
        background-color: $light;
        background-image: url("https://wallbox.ru/resize/1600x1200/wallpapers/main2/201725/14978796235947d447bf7440.59089179.jpg");
        min-height: 300px;
        opacity: 0.9;
        box-shadow: 0 0 30px 20px rgba(122,122,122,0.5);
    }

    .logo-footer {
        text-align: center;
        font-size: 35px;
        color: #000;
    }

    .container-footer{
        width: 100%;
    }

    .row-footer{
        margin: auto;
    }

    .item-footer{
        text-align: center;
        border-radius: 10px;
        height: 30px;
        width: 95%;
        background: rgba(33,37,41, 0.7);
        font-size: 20px;
        margin: 10px;
        transition: 0.3s;
    }
   .item-footer:hover{
        height: 32px;
        width: 96%;
        background:  rgba(107,98,108, 0.6);
    }

    ul.list-unstyled li a{
        color: #000;
        text-decoration: none;
    }

    .year-footer{
        text-align: center;
        margin-top: 20px;
        font-size: 20px;
        color: #000;
    }

    .support{
        display: block;
        text-align: center;
        border-radius: 10px;
        background: rgba(33,37,41, 0.7);
        font-size: 15px;
        margin: 0 auto;
        height: 40px;
        width: 75%;
        transition: 0.3s;
    }
}
@media(min-width: 1024px){
    .footer {
        padding: 1rem 0 0 0;
        margin: 0;
        background-color: $light;
        background-image: url("https://wallbox.ru/resize/1600x1200/wallpapers/main2/201725/14978796235947d447bf7440.59089179.jpg");
        min-height: 300px;
        opacity: 0.9;
        box-shadow: 0 0 30px 20px rgba(122,122,122,0.5);
    }

    .logo-footer {
        text-align: center;
        font-size: 35px;
        color: #000;
    }

    .container-footer{
        width: 80%;
    }

    .row-footer{
        margin: auto;
    }

    .item-footer{
        text-align: center;
        border-radius: 10px;
        height: 35px;
        width: 95%;
        background: rgba(33,37,41, 0.7);
        font-size: 25px;
        margin: 10px;
        transition: 0.3s;
    }
    .item-footer:hover{
        height: 37px;
        width: 96%;
        background:  rgba(107,98,108, 0.6);
    }

    ul.list-unstyled li a{
        color: #000;
        text-decoration: none;
    }

    .year-footer{
        text-align: center;
        margin-top: 20px;
        font-size: 20px;
        color: #000;
    }

    .support{
        display: block;
        text-align: center;
        border-radius: 10px;
        background: rgba(33,37,41, 0.7);
        font-size: 20px;
        margin: 0 auto;
        height: 45px;
        width: 50%;
        transition: 0.3s;
    }
}
@media(min-width: 1440px){
    .footer {
        padding: 1rem 0 0 0;
        background-color: $light;
        background-image: url("https://wallbox.ru/resize/1600x1200/wallpapers/main2/201725/14978796235947d447bf7440.59089179.jpg");
        min-height: 300px;
        opacity: 0.9;
        box-shadow: 0 0 30px 20px rgba(122,122,122,0.5);
    }

    .logo-footer {
        text-align: center;
        font-size: 45px;
        color: #000;
    }

    .container-footer{
        width: 65%;
    }

    .row-footer{
        margin: auto;
    }

    .item-footer{
        text-align: center;
        border-radius: 10px;
        height: 40px;
        width: 97%;
        background: rgba(33,37,41, 0.7);
        font-size: 30px;
        margin: 10px;
        transition: 0.3s;
    }
    .item-footer:hover{
        height: 42px;
        width: 98%;
        background:  rgba(107,98,108, 0.6);
    }

    ul.list-unstyled li a{
        color: #000;
        text-decoration: none;
    }

    .year-footer{
        text-align: center;
        margin-top: 20px;
        font-size: 20px;
        color: #000;
    }

    .support{
        display: block;
        text-align: center;
        border-radius: 10px;
        background: rgba(33,37,41, 0.7);
        font-size: 20px;
        margin: 0 auto;
        height: 45px;
        width: 18%;
        transition: 0.3s;
    }
}
div.col-md-8 nav ul.pagination{
    display: flex;

}
div.col-md-8 nav ul.pagination li.page-item{
    flex-grow: 1;
    text-align: center;
    font-size: 20px;
    
}
div.col-md-8 nav ul.pagination li.page-item a{
    color: #444546;
    transition: 0.3s;
}
div.col-md-8 nav ul.pagination li.page-item a:hover{
    background:  rgba(107,98,108, 0.6);
}
div.col-md-8 nav ul.pagination li.page-item a:active{
    border: none;
}
div.col-md-8 nav ul.pagination li.active a{
    background:  rgba(123,121,127, 1);
    border-color: rgba(123,121,127, 1);
    color: #000;
}
nav ul.pagination li.page-item span{
    color: #000;
}
a:hover{
    text-decoration: none;
}
</style>
