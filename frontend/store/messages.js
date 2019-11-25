export const state = () => {
    return {
        /**
         * Список сообщений
         * @type {Array}
         */
        messages: [],
    }
}

export const getters = {

    messagesList(state) {
        return state.messages;
    },

}


export const mutations = {

    SET_MESSAGES(state, messages) {
        state.messages = messages;
    },

    // REMOVE_ITEM(state, postId) {
    //     state.posts.forEach((item, index) => {
    //         if (item.id === postId) {
    //             state.posts.splice(index, 1);
    //         }
    //     })
    // },

}


export const actions = {

    /**
     * Получение сообщений
     * @param  {[type]} context [description]
     * @return
     */
    async getMessages(context) {
        let _param = new URLSearchParams();
        _param.append('link', $nuxt.$route.params.link);

        let data = await this.$axios.$post('/api/messages/list/index', _param).then((res) => {
            return res;
        }).catch((error) => {
            return [];
        });

        context.commit('SET_MESSAGES', data);
    },



    /**
     * Отправляем текстовое сообщение
     * @param  {[type]} context [description]
     * @param  string   text    текст сообщения
     * @return string
     */
    async textMessages(context, text) {
        let _param = new URLSearchParams();
        _param.append('link', $nuxt.$route.params.link);
        _param.append('text', text);

        let data = await this.$axios.$post('/api/messages/create/text-message', _param).then((res) => {
            return res;
        }).catch((error) => {
            return { result: 'error', messages: 'Возникла ошибка' };
        });

        context.dispatch('getMessages');

        if (data.result == 'success') {
            return '';
        }

        return data.messages;
    },



    /**
     * Отправляем текстовое сообщение
     * @param  {[type]} context [description]
     * @param  file     file    файл для отправки
     * @return string
     */
    async fileMessages(context, file) {
        let formData = new FormData();
        formData.append('file', file);
        formData.append('link', $nuxt.$route.params.link);

        let data = await this.$axios.$post('/api/messages/create/file-message',
                                           formData,
                                           {headers: {'Content-Type': 'multipart/form-data'}}).then((res) => {
            return res;
        }).catch((error) => {
            return { result: 'error', messages: 'Возникла ошибка' };
        });

        console.log(data);

        return '';

        // context.dispatch('getMessages');

        // if (data.result == 'success') {
        //     return '';
        // }

        // return data.messages;
    },

}
