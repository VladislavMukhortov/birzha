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
     * @param array data
     */
    SET_CROP_LIST(state, data) {
        state.cropList = data;
    },
}
