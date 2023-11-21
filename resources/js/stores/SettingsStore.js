import { defineStore } from "pinia"
import SettingsApi from '@/api/Settings'

export const useSettingsStore = defineStore('settings', {
    state: () => ({
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
    }),
    actions: {
        setSettings (settings) {
            this.settings = settings
        },
        setLoading (isLoading) {
            this.isLoading = isLoading
        },
        setSaving (isSaving) {
            this.isSaving = isSaving
        },

        getSettings() {
            return new Promise((resolve, reject) => {
                this.setLoading(true)
                
                SettingsApi.getSettings()
                    .then(resp => {
                        this.setSettings(resp.data.data)

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
        updateSettings(payload) {
            return new Promise((resolve, reject) => {
                this.setSaving(true)
                SettingsApi.updateSettings(payload)
                    .then(resp => {
                        // TODO: set success!
                        resolve(resp)
                    })
                    .catch(err => {
                        reject(err)
                    })
                    .finally(() => {
                        this.setSaving(false)
                    })
            })
        }
    }
})