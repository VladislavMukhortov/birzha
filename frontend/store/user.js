export const state = () => {
    return {
        lang: 'ru-RU'   // en-US ru-RU
    }
}

export const mutations = {
    setLanguage(state, lang) {
        state.lang = lang;
    },
}
