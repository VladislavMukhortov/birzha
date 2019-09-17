import Vue from 'vue';


Vue.mixin({
    methods: {
        /**
         * отчистка строки от посторонних символов
         */
        cleanStr: function(str) {
            return str.toString().replace(/<[^>]*>/g, '').replace(/\r|\n|\t/g, ' ').replace(/ {2,}/g, ' ').trim();
        },

        /**
         * валидация номера телефона
         */
        // checkPhone: function(phone) {
        //     return phone.toString().replace(/[^+0-9]/gim, '');
        // }
    }
})
