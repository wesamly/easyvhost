import SettingsApi from './../api/Settings'

const state = {
    settings: {
        configs: {
            default: {
                tags: [],
                file: ''
            },
            files: []
        }
    },
    isLoading: false,
    isSaving: false,
}


const mutations = {
    setSettings (state, settings) {
        state.settings = settings
    },
    setLoading (state, isLoading) {
        state.isLoading = isLoading
    },
    setSaving (state, isSaving) {
        state.isSaving = isSaving
    }
}

const actions = {
    getSettings({ commit }) {
        return new Promise((resolve, reject) => {
            commit('setLoading', true)
            
            SettingsApi.getSettings()
                .then(resp => {
                    commit('setSettings', resp.data.data)
                    resolve(resp)
                })
                .catch(err => {
                    reject(err)
                })
                .finally(() => {
                    commit('setLoading', false)
                })
        })
    },
    updateSettings({ commit }, payload) {
        return new Promise((resolve, reject) => {
            commit('setSaving', true)
            SettingsApi.updateSettings(payload)
                .then(resp => {
                    commit('setSuccess', true)
                    resolve(resp)
                })
                .catch(err => {
                    reject(err)
                })
                .finally(() => {
                    commit('setSaving', false)
                })
        })
    }
}

const getters = {
    isLoading: state => state.isLoading,
    isSaving: state => state.isSaving,
}

export default {
    namespaced: true,
    state: state,
    mutations: mutations,
    actions: actions,
    getters: getters
};