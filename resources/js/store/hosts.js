import HostsApi from './../api/Hosts'

const state = {
    hosts: [],
    pagination: {},
    currentHost: {id: '', domain: ''},
    isLoading: false,
    isSaving: false,
    isSuccess: false,
}

const mutations = {
    setHostsData (state, data) {
        state.hosts = data.data
        state.pagination = data.meta
    },
    setCurrentHost (state, data) {
        state.currentHost = data.data
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
    getHosts({ commit }, payload) {
        return new Promise((resolve, reject) => {
            commit('setLoading', true)
            
            HostsApi.getHosts(payload)
                .then(resp => {
                    commit('setHostsData', resp.data)
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
    getHostsList({ dispatch }, payload) {
        return dispatch('getHosts', {configs: 'DocumentRoot, _ip_port', per_page: 99999})
    },
    addHost({ commit }, host) {
        return new Promise((resolve, reject) => {
            commit('setSaving', true)
            
            HostsApi.createHost(host)
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
    setCurrentHost({ commit }, id) {
        return new Promise((resolve, reject) => {
            commit('setLoading', true)
            
            HostsApi.getHost(id)
                .then(resp => {
                    commit('setCurrentHost', resp.data)
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
    updateHost({ commit }, payload) {
        return new Promise((resolve, reject) => {
            commit('setSaving', true)
            
            HostsApi.updateHost(payload.id, payload.host)
                .then(resp => {
                    commit('setSuccess', true)
                    //commit('flash/addMessage', {type: 'success', text: resp.data.meta.message, when: 'now'},{ root: true })
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
    deleteHost({ commit }, id) {
        return new Promise((resolve, reject) => {
            commit('setSaving', true)
            
            HostsApi.deleteHost(id)
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
    },
    resetCurrentHost({commit}) {
        let data = {
            id: 0,
            domain: '',
            configs: {
                _addr_port: '',
                ServerName: '',
                DocumentRoot: '',
            },
            tags: []
        }

        commit('setCurrentHost', {data: data})

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