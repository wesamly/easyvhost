import axios from 'axios'

export default {
    getTags (payload) {
        return axios.get('api/tags', {
            params: payload,
        })
    },
    getTag (id) {
        return axios.get('api/tags/' + id)
    },
    createTag (payload) {
        return axios.post('api/tags', payload)
    },
    updateTag (id, payload) {
        payload._method = 'PATCH'
        return axios.post('api/tags/' + id, payload)
    },
    deleteTag (id) {
        return axios.delete('api/tags/' + id)
    }
}