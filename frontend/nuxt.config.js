
export default {
    mode: 'spa',    // spa universal

    /*
    ** Headers of the page
    */
    head: {
        title: process.env.npm_package_name || '',
        meta: [
            { charset: 'utf-8' },
            { name: 'viewport', content: 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui' },
            { hid: 'description', name: 'description', content: process.env.npm_package_description || '' },
            { hid: 'keywords', name: 'keywords', content: '' },
            { name: 'application-name', content: 'Grain Market' }
        ],
        link: [
            { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
        ]
    },

    /*
    ** Customize the progress-bar color
    */
    loading: { color: '#fff' },

    /*
    ** Global CSS
    */
    css: [
        '~/assets/scss/app.scss'
    ],

    /*
    ** Plugins to load before mounting the App
    */
    plugins: [
        { src: '~/plugins/bootstrap.js' },
        { src: '~/plugins/axios.js', ssr: false },
        { src: '~/plugins/core.js', ssr: false }
    ],

    /*
    ** Nuxt.js modules
    */
    modules: [
        // https://github.com/nuxt-community/webpackmonitor-module
        // https://github.com/schlunsen/nuxt-leaflet
        // 'nuxt-i18n',                // https://nuxt-community.github.io/nuxt-i18n
        '@nuxtjs/axios',            // Doc: https://axios.nuxtjs.org/usage
        '@nuxtjs/auth',             // https://auth.nuxtjs.org/guide
        '@nuxtjs/style-resources',  // https://www.npmjs.com/package/@nuxtjs/style-resources
        // '@nuxtjs/pwa',
        '@nuxtjs/sitemap'           // https://github.com/nuxt-community/sitemap-module
    ],

    /*
    ** Nuxt.js Nuxt-i18n
    */
    // i18n: {
    //     baseUrl: 'http://ncc.local',
    //     // baseUrl: 'http://dev.russiangrainmarket.ru',
    //     locales: [
    //         {
    //             code: 'en',
    //             iso: 'en-US'
    //         },
    //         {
    //             code: 'ru',
    //             iso: 'ru-RU'
    //         }
    //     ],
    //     defaultLocale: 'en',
    // },

    /*
    ** Nuxt.js @nuxtjs/auth
    */
    auth: {
        strategies: {
            local: {
                endpoints: {
                    login: false,
                    user: false,
                    logout: false
                },
                tokenRequired: true,
                tokenType: false,
                tokenName: 'X-Api-Key'
            }
        },
        redirect: {
            login: '/auth/signin?login=1',
            logout: '/?logout=1',
            home: '/market?home=1',
            callback:'/?callback=1'
        }
    },

    router: {
        middleware: ['auth']
    },

    /*
    ** Nuxt.js Style-resources
    */
    styleResources: {
        scss: ['~/assets/scss/_variables.scss']
    },

    /*
    ** Axios module configuration
    ** See https://axios.nuxtjs.org/options
    */
    axios: {
        // See https://github.com/nuxt-community/axios-module
        //baseURL: 'http://gm.local',
        baseURL: 'http://gm.russiangrainmarket.ru',
        https: false
    },

    /*
    ** Nuxt.js @nuxtjs/pwa manifest
    */
    // manifest: {
    //     name: process.env.npm_package_name || '',
    //     short_name: 'Grain Market',
    //     lang: 'ru',
    //     description: '',
    //     theme_color: '#fff',
    //     background_color: '#fff'
    // },

    /*
    ** Nuxt.js @nuxtjs/pwa meta
    */
    // meta: {
    //     charset: 'utf-8',
    //     viewport: 'width=device-width, initial-scale=1',
    //     mobileApp: true,
    //     mobileAppIOS: true,
    //     lang: 'ru',
    //     dir: 'ltr',
    //     ogType: 'website',
    //     ogSiteName: 'Grain Market',
    //     ogTitle: process.env.npm_package_name || '',
    //     ogDescription: '',
    //     ogHost: 'https://russiangrainmarket.ru',
    //     ogImage: true
    // },

    /*
    ** Nuxt.js @nuxtjs/sitemap
    */
    sitemap: {
        hostname: 'https://russiangrainmarket.ru',
        gzip: true,
    },

    /*
    ** Build configuration
    */
    build: {
        /*
        ** You can extend webpack config here
        */
        extend(config, ctx) {
        }
    }
}
