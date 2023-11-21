import { defineStore } from "pinia"
import TagsApi from '@/api/Tags'

export const useTagsStore = defineStore('tags', {
    state: () => ({
        tags: [],
        pagination: {},
        currentTag: {},
        isLoading: false,
        isSaving: false,
        isDeleting: false,
        isSuccess: false
    }),
    actions: {
        setTagsData (data) {
            this.tags = data.data
            this.pagination = data.meta
        },
        setCurrentTag (data) {
            this.currentTag = data.data
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

        getTags(payload) {
            return new Promise((resolve, reject) => {
                this.setLoading(true)
                
                TagsApi.getTags(payload)
                    .then(resp => {
                        this.setTagsData(resp.data)
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
        addTag(tag) {
            return new Promise((resolve, reject) => {
                this.setSaving(true)
                
                TagsApi.createTag(tag)
                    .then(resp => {
                        resolve(resp)
                    })
                    .catch(err => {
                        reject(err)
                    })
                    .finally(() => {
                        this.setSaving(false)
                    })
            })
        },
        setCurrentTag(id) {
            return new Promise((resolve, reject) => {
                this.setLoading(true)
                
                TagsApi.getTag(id)
                    .then(resp => {
                        this.setCurrentTag(resp.data)
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
        updateTag(payload) {
            return new Promise((resolve, reject) => {
                this.setSaving(true)
                
                TagsApi.updateTag(payload.id, payload.tag)
                    .then(resp => {
                        this.setSuccess(true)
                        resolve(resp)
                    })
                    .catch(err => {
                        reject(err)
                    })
                    .finally(() => {
                        this.setSaving(false)
                    })
            })
        },
        deleteTag(id) {
            return new Promise((resolve, reject) => {
                this.setDeleting(true)
                
                TagsApi.deleteTag(id)
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
        }
    }
})