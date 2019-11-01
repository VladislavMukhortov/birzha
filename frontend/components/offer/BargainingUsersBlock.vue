// Блок с запросами на подтверждение "твердо" и с "твердо"
// можно дать твердо, если нет других офферов со статусом "твердо"
// так же показывает уведомления о взаимодействии

<template>

    <div>

        <b-form-group label-class="required" label="цена:">
                            <b-input type="text" v-model="price"></b-input>
                        </b-form-group>

                        <b-button
                            v-if="deal == 'sell'"
                            variant="secondary"
                            v-on:click="newPrice">Bid (понизить) цену</b-button>
                        <b-button
                            v-else
                            variant="secondary"
                            v-on:click="newPrice">Push (повысить) цену</b-button>

        <pre>{{ deal }}</pre>
        <pre>{{ offer }}</pre>
    </div>

</template>




<script>
export default {
    props: {
        offer: {
            type: Object
        },
        deal: {
            type: String
        },
    },

    methods: {
        newPrice() {
            console.log('123');
        },
    },
};
</script>



<style lang='scss'>
</style>
