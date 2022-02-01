import TagsApi from './../api/Tags'

const state = {
    tags: [],
    pagination: {},
    currentTag: {},
    isLoading: false,
    isSaving: false,
    isSuccess: false,
}

const mutations = {
    setTagsData (state, data) {
        state.tags = data.data
        state.pagination = data.meta
    },
    setCurrentTag (state, data) {
        state.currentTag = data.data
    },
    setLoading (state, isLoading) {
        // mutate state
        state.isLoading = isLoading
    },
    setSaving (state, isSaving) {
        // mutate state
        state.isSaving = isSaving
    },
    setSuccess (state, isSuccess) {
        // mutate state
        state.isSuccess = isSuccess
    },
    
}

const actions = {
    getTags({ commit }, payload) {
        return new Promise((resolve, reject) => {
            commit('setLoading', true)
            
            TagsApi.getTags(payload)
                .then(resp => {
                    commit('setTagsData', resp.data)
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
    addTag({ commit }, tag) {
        return new Promise((resolve, reject) => {
            commit('setSaving', true)
            
            TagsApi.createTag(tag)
                .then(resp => {
                    //commit('flash/addMessage', {type: 'success', text: resp.data.meta.message, when: 'next'},{ root: true })
                    resolve(resp)
                })
                .catch(err => {
                    
                    if (Object.prototype.hasOwnProperty.call(err, 'response') && Object.prototype.hasOwnProperty.call(err.response, 'status') && err.response.status == 422) {
                        commit('form/setInputErrors', err.response.data.errors, { root: true })
                    }
                    reject(err)
                })
                .finally(() => {
                    commit('setSaving', false)
                })
        })
    },
    setCurrentTag({ commit }, id) {
        return new Promise((resolve, reject) => {
            commit('setLoading', true)
            
            TagsApi.getTag(id)
                .then(resp => {
                    commit('setCurrentTag', resp.data)
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
    updateTag({ commit }, payload) {
        return new Promise((resolve, reject) => {
            commit('setSaving', true)
            
            TagsApi.updateTag(payload.id, payload.tag)
                .then(resp => {
                    commit('setSuccess', true)
                    commit('flash/addMessage', {type: 'success', text: resp.data.meta.message, when: 'now'},{ root: true })
                    resolve(resp)
                })
                .catch(err => {
                    if (Object.prototype.hasOwnProperty.call(err, 'response') && Object.prototype.hasOwnProperty.call(err.response, 'status') && err.response.status == 422) {
                        commit('form/setInputErrors', err.response.data.errors, { root: true })
                    }
                    reject(err)
                })
                .finally(() => {
                    commit('setSaving', false)
                })
        })
    },
    deleteTag({ commit }, id) {
        return new Promise((resolve, reject) => {
            commit('setSaving', true)
            
            TagsApi.deleteTag(id)
                .then(resp => {
                    commit('flash/addMessage', {type: 'success', text: resp.data.meta.message, when: 'now'},{ root: true })
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