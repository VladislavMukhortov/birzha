export default function ({ $axios, store }) {

    // https://github.com/axios/axios/issues/97

    $axios.defaults.headers['Content-Type'] = 'application/x-www-form-urlencoded';
    $axios.defaults.headers['Accept-Language'] = store.state.user.lang;

    $axios.onRequest(config => {
        config.headers['Accept-Language'] = store.state.user.lang;

        // if (store.state.auth.token) {
        //     config.headers['X-Api-Key'] = store.state.auth.token
        // }

        // console.log('onRequest')
        // console.log(config)
    })

    $axios.onResponse(response => {
        //
    })

    $axios.onError(err => {
        // console.log('onError')
        // console.log(err)

        // const code = parseInt(error.response && error.response.status)
        // if (code === 400) {
        //     redirect('/400')
        // }
    })


// https://axios.nuxtjs.org/helpers
}
