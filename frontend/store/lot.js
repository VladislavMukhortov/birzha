export const state = () => {
    return {
        // список типов сделки (покупка / продажа)
        dealList: [
            { value : 'sell', text : 'Selling' },
            { value : 'buy', text : 'Buying' },
        ],

        // список валют, которые используются на сайте
        currencyList: [
            { id : '1', name : 'USD' },
            { id : '2', name : 'EUR' },
            { id : '3', name : 'RUB' },
            { id : '4', name : 'UAH' },
            { id : '5', name : 'KZT' },
            { id : '6', name : 'CNY' },
        ],

        // список культур которые используются на сайте
        cropList: [],
    }
}


export const mutations = {
    /**
     * Устанавливаем список культур на сайте
     * @param {[type]} state
     * @param array payload
     */
    SET_CROP_LIST(state, payload) {
        state.cropList = payload;
    },
}


export const actions = {
    /**
     * Устанавливаем список культур на сайте
     */
    async GET_CROP_LIST(context) {
        let data = await this.$axios.$get('/api/crop/list/market').then((res) => {
            return res;
        });
        context.commit('SET_CROP_LIST', data);
    },
}
