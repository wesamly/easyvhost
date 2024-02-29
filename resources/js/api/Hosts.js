import axios from 'axios'

export default {
    getHosts (payload) {
        return axios.get('/api/hosts', {
            params: payload,
        })
    },
    getHost (id) {
        return axios.get('/api/hosts/' + id)
    },
    createHost (payload) {
        return axios.post('/api/hosts', payload)
    },
    updateHost (id, payload) {
        payload._method = 'PATCH'
        return axios.post('/api/hosts/' + id, payload)
    },
    deleteHost (id) {
        return axios.delete('/api/hosts/' + id)
    }
}