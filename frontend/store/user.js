export const state = () => {
    return {
        // язык приложения, отображаемый для пользователя
        lang: 'ru-RU',
        // список языков для приложения
        langList: ['en-US', 'ru-RU'],
    }
}

export const mutations = {
    setLanguage(state, lang) {
        state.lang = lang;
    },
}
