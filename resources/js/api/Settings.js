import axios from 'axios'

export default {
    getSettings () {
        
        return axios.get('/api/settings')
    },
    
    updateSettings (payload) {
        return axios.post('/api/settings', payload)
    },
}
  