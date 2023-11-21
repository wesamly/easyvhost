import { defineStore } from 'pinia'
import HostsApi from '@/api/Hosts'

export const useHostsStore = defineStore('hosts', {
    state: () => ({
        hosts: [],
        pagination: {},
        currentHost: { id: '', domain: '' },
        isLoading: false,
        isSaving: false,
        isDeleting: false,
        isSuccess: false,
    }),
    actions: {
        setHostsData (data) {
            this.hosts = data.data
            this.pagination = data.meta
        },
        assignCurrentHost (data) {
            this.currentHost = data.data
        },
        setLoading (isLoading) {
            this.isLoading = isLoading
        },
        setSaving (isSaving) {
            this.isSaving = isSaving
        },
        setDeleting (isDeleting) {
            this.isDeleting = isDeleting
        },
        setSuccess (isSuccess) {
            this.isSuccess = isSuccess
        },
        getHosts(payload) {
            return new Promise((resolve, reject) => {
                this.setLoading(true)
                
                HostsApi.getHosts(payload)
                    .then(resp => {
                        this.setHostsData(resp.data)
                        resolve(resp)
                    })
                    .catch(err => {
                        reject(err)
                    })
                    .finally(() => {
                        this.setLoading(false)
                    })
            })
        },
        getHostsList(payload) {
            return this.getHosts({configs: 'ServerName, DocumentRoot, _ip_port', per_page: 99999})
        },
        addHost(host) {
            return new Promise((resolve, reject) => {
                this.setSaving(true)
                
                HostsApi.createHost(host)
                    .then(resp => {
                        resolve(resp)
                    })
                    .catch(err => {
                        
                        if (Object.prototype.hasOwnProperty.call(err, 'response') && Object.prototype.hasOwnProperty.call(err.response, 'status') && err.response.status == 422) {
                            // TODO: commit('form/setInputErrors', err.response.data.errors, { root: true })
                        }
                        reject(err)
                    })
                    .finally(() => {
                        this.setSaving(false)
                    })
            })
        },
        setCurrentHost(id) {
            return new Promise((resolve, reject) => {
                this.setLoading(true)

                HostsApi.getHost(id)
                    .then(resp => {
                        this.assignCurrentHost(resp.data)
                        resolve(resp)
                    })
                    .catch(err => {
                        reject(err)
                    })
                    .finally(() => {
                        this.setLoading(false)
                    })
            })
        },
        updateHost(payload) {
            return new Promise((resolve, reject) => {
                this.setSaving(true)

                HostsApi.updateHost(payload.id, payload.host)
                    .then(resp => {
                        this.setSuccess(true)
                        resolve(resp)
                    })
                    .catch(err => {
                        if (Object.prototype.hasOwnProperty.call(err, 'response') && Object.prototype.hasOwnProperty.call(err.response, 'status') && err.response.status == 422) {
                            // TODO: commit('form/setInputErrors', err.response.data.errors, { root: true })
                        }
                        reject(err)
                    })
                    .finally(() => {
                        this.setSaving(false)
                    })
            })
        },
        deleteHost(id) {
            return new Promise((resolve, reject) => {
                this.setDeleting(true)

                HostsApi.deleteHost(id)
                    .then(resp => {
                        resolve(resp)
                    })
                    .catch(err => {
                        reject(err)
                    })
                    .finally(() => {
                        this.setDeleting(false)
                    })
            })
        },
        resetCurrentHost() {
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
    
            this.assignCurrentHost({data: data})    
        }
    }
})