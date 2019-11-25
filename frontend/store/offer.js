export const state = () => {
    return {

        /**
         * список запросов ожадиющих "твердо" и имеющих "твердо"
         * каждый элемент имеет такую структуру
         * created_at: string
         * ended_at: string
         * link: string
         * status: bool
         * time: integer
         *
         * @type {Array}
         */
        auctionList: [],
    }
}


export const mutations = {
    /**
     * Устанавливаем список запросов ожидающих "твердо" и имеющих "твердо"
     * @param {[type]} state
     * @param array payload
     */
    SET_AUCTION_LIST(state, payload) {
        state.auctionList = payload;
    },

    /**
     * Устанавливаем параметр time в элемент массива auctionList
     * @param {[type]} state   [description]
     * @param Object   payload index - номер элемента в массиве / val значение которое надо присвоить
     */
    SET_AUCTION_LIST_TIME_BY_IDX(state, payload) {
        state.auctionList[payload.index].time = payload.val || 30;
    }
}


export const actions = {
    /**
     * Получаем список запросов ожидающих "твердо" и имеющих "твердо"
     */
    async GET_AUCTION_LIST(context) {
        let _param = {params: {
            link: $nuxt.$route.params.link,
        }};

        let data = await this.$axios.$get('/api/offer/show/my-order', _param).then((res) => {
            return res;
        }).catch((error) => {
            return { result: 'error', data: [], };
        });

        if (data.result === 'success') {
            context.commit('SET_AUCTION_LIST', data.data);
        }

    },
}
