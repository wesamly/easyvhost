import axios from 'axios'

export default {
    getHosts (payload) {
        // if (payload.per_page == -1) {
        //     delete payload.per_page
        // }
        
        return axios.get('/api/hosts', {
            params: payload,
            // paramsSerializer: function (params) {
            //     return Qs.stringify(params, {arrayFormat: 'brackets'})
            // },
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