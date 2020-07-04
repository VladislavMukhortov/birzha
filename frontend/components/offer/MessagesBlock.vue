// Блок с запросами на подтверждение "твердо" и с "твердо"
// можно дать твердо, если нет других офферов со статусом "твердо"
// так же показывает уведомления о взаимодействии

<template>

    <div>

        <div id="js-messages-list" class="messages-list">
            <template v-if="messages.length">
                <div
                    v-for="item in messages"
                    class="messages-item"
                    v-bind:class="{ '_im-sent': item.sent }"
                    v-bind:key="item.id">
                    <div class="text-muted" v-text="item.created_at"></div>
                    <template v-if="item.type == 'text'">
                        <div class="messages-item-text">
                            <span v-text="item.text"></span>
                            <span v-if="item.translation" v-text="item.translation"></span>
                        </div>
                    </template>
                    <template v-else-if="item.type == 'img'">
                        <v-link v-bind:href="item.text" v-text="item.text"></v-link>
                    </template>

                </div>
            </template>
            <template v-else>
                <b class="text-muted">Сообщений нет</b>
            </template>
        </div>

        <hr>

        <p>Написать сообщение</p>

        <b-form-group>
            <b-input
                type="text"
                v-model="messageText"
                v-bind:state="messageTextState"></b-input>
            <b-form-invalid-feedback>{{ messageTextStateText }}</b-form-invalid-feedback>
        </b-form-group>
        <div class="custom-btn">
            <b-form-group>
                <b-button
                    type="button"
                    variant=""
                    v-on:click="sendTextMessages"
                    v-bind:class="{ 'disabled': !isActiveSubmitBtn }">Отправить</b-button>
            </b-form-group>
        </div>


        <b-form-group>
            <!-- <b-form-file v-model="file" ref="file-input"></b-form-file> -->
            <p>Отправить файл</p>
            <!-- <b-form-file
                v-model="file"
                id="file"
                :ref="file"
                placeholder="Choose a file or drop it here..."
                drop-placeholder="Drop file here..."
                v-on:change="handleFileUpload"></b-form-file> -->

            <label><input type="file" id="file" ref="file" v-on:change="handleFileUpload"/></label>
        </b-form-group>

        <div class="users-wrapper">
            <h5 class="users-header">Информация о собеседнике:</h5>
            <div class="users-items">
                Имя: <b>{{userName}}</b><br>
                Телефон: <b>{{userPhone}}</b><br>
                e-mail: <b>{{userEmail}}</b><br>
                Должность: <b>{{userRole}}</b><br>
            </div>
        </div>
    </div>


</template>




<script>
export default {
    data() {
        return {
            // file: null,                 // файл для отправки
            messageFileState: null,     // состояние файла сообщения
            messageFileStateText: '',   // текст ошибки для отправки файла

            messageText: '',            // текст сообщения который отправляется собеседнику
            messageTextState: null,     // состояние текста сообщения
            messageTextStateText: '',   // текст ошибки для отправки текста

            userData: "test",
            userName: "",               // имя собеседника
            userEmail: "",              // почта собеседника
            userPhone: "",              // номер собеседника
            userRole: "",               // должность собеседника
        }
    },

    mounted() {
        this.$store.dispatch('messages/getMessages');

        this.getUser();
    },

    computed: {
        /**
         * @return array список сообщений
         */
        messages() {
            setTimeout(function() {
                let messagesList = document.getElementById("js-messages-list");
                if (messagesList) {
                    messagesList.scrollTop = messagesList.scrollHeight;
                }
            }, 200);

            return this.$store.getters['messages/messagesList'];
        },

        /**
         * Проверка наличия текста для блокировки кнопки "Submit"
         * @return boolean
         */
        isActiveSubmitBtn() {
            return (this.messageText.length) ? true : false;
        },
    },

    methods: {

        /**
         * отправляем текст собеседнику
         */
        async sendTextMessages() {
            this.messageTextState = null;
            this.messageTextStateText = '';

            if (!this.messageText.length) {
                this.messageTextStateText = 'Введите текст сообщения';
                this.messageTextState = false;
            }

            // пустая строка или текст ошибки
            let statusSendMessage = await this.$store.dispatch('messages/textMessages', this.messageText);

            if (statusSendMessage) {
                // сообщение не отправленно
                this.messageTextStateText = statusSendMessage;
                this.messageTextState = false;
            }
        },

        async getUser(){
            let res = await this.$axios.$post('/api/offer/list/get-user-by-id').then((res) => {
                return res;
            }).catch((error) => {
                return {
                    result: 'error',
                    messages: 'error',
                };
            });

            this.userData = res;
            this.userName = res.user.name;
            this.userPhone = res.user.phone;
            this.userEmail = res.user.email;
            if(res.user.position){
                this.userRole = res.user.position;
            }
            else{
                this.userRole = "должность не указана";
            }

        },


        async handleFileUpload() {
            let file = this.$refs.file.files[0];
            console.log(file);

            // this.messageFileState = null;
            // this.messageFileStateText = '';

            // // пустая строка или текст ошибки
            let statusSendMessage = await this.$store.dispatch('messages/fileMessages', file);

            // if (statusSendMessage) {
            //     // сообщение не отправленно
            //     this.messageFileStateText = statusSendMessage;
            //     this.messageFileState = false;
            // }

        },
    },
};
</script>



<style lang='scss'>
.messages-list {
    padding: 0.5rem;
    border: 1px solid #000;
    max-height: 400px;
    overflow-x: hidden;
    overflow-y: auto;
}
.messages-item {
    padding: 0.5rem;
    margin: 0 50px 15px 0;
    background: #e4e4e4;
    text-align: left;

    &._im-sent {
        margin-right: 0px;
        margin-left: 50px;
        text-align: right;
    }

    &:last-child {
        margin-bottom: 0px;
    }
}
.users-wrapper{
    float: right;
    border: 1px #d0c8d0 solid;
    padding: 10px;
    border-radius: 10px;
    width: 100%;
    margin-top: 20px;
}
.users-header{
    margin-top: 16.5px;
}
.users-items{
    margin-top: 24px;
}
@media (max-width: 768px){
    .custom-btn button{
        width: 100%;
        display: inline-block;
        background: rgba(123,121,127, 1) !important;
        border-radius: 10px !important;
        border: 1px #000 solid !important;
        color: #000 !important;
        text-align: center;
        transition: 0.3s;
    }
    .custom-btn button:hover{
        background:  rgba(107,98,108, 0.6) !important;
        border-color: rgba(107,98,108, 0.6) !important;
        color: #000 !important;
    }
}
@media (min-width: 1024px){
    .custom-btn button{
        width: 45%;
        display: inline-block;
        background: rgba(123,121,127, 1) !important;
        border-radius: 10px !important;
        border: 1px #000 solid !important;
        color: #000 !important;
        text-align: center;
        transition: 0.3s;
    }
    .custom-btn button:hover{
        background:  rgba(107,98,108, 0.6) !important;
        border-color: rgba(107,98,108, 0.6) !important;
        color: #000 !important;
    }
}

</style>
