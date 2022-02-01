import axios from 'axios'

export default {
    getTags (payload) {
        // if (payload.per_page == -1) {
        //     delete payload.per_page
        // }
        
        return axios.get('/api/tags', {
            params: payload,
            // paramsSerializer: function (params) {
            //     return Qs.stringify(params, {arrayFormat: 'brackets'})
            // },
        })
    },
    getTag (id) {
        return axios.get('/api/tags/' + id)
    },
    createTag (payload) {
        return axios.post('/api/tags', payload)
    },
    updateTag (id, payload) {
        payload._method = 'PATCH'
        return axios.post('/api/tags/' + id, payload)
    },
    deleteTag (id) {
        return axios.delete('/api/tags/' + id)
    }
}