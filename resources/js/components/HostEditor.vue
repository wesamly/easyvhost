<template>
    <div class="">
        <h5>{{ editorTitle }}</h5>

        <form action="" method="post" @submit.prevent="saveHost" id="hostForm">
            <div class="row mb-3">
                <label for="domain" class="col-sm-2 col-form-label">Domain (Title)</label>
                <div class="col-sm-10">
                <input type="text" :class="['form-control', {'is-invalid': fieldHasError('domain')}]" id="domain" v-model="host.domain" placeholder="example.com">
                <div class="invalid-feedback" v-if="fieldHasError('domain')">{{ getFieldError('domain') }}</div>
                </div>
            </div>
            
            <section-editor v-model="configs" />
            <section-editor section="https" v-model="configs" />
            
            <hr>

            <fieldset>
                <legend>Tags</legend>
                
                <tags-input v-model="host.tags" class="mb-3"></tags-input>
            </fieldset>

            <div class="alert alert-success" v-if="message != ''">
                <i class="bi bi-check-circle-fill"></i>
                {{ message }}
            </div>

            <button type="submit" class="btn btn-primary" :disabled="isSaving"><i class="bi bi-arrow-clockwise" animation="spin" v-if="isSaving"></i> Submit</button>
            <button type="button" class="btn btn-danger float-end" @click="confirmDeletion" :disabled="isDeleting"><i class="bi bi-arrow-clockwise" animation="spin" v-if="isDeleting"></i> Delete</button>
        </form>

        <confirm ref="delConfirm" @confirmed="deleteCurrentHost" :content="`This will delete ${host.domain} virtual host. Continue?`"></confirm>
    </div>
</template>

<script>
    import { mapState, mapActions } from 'pinia'
    import { useHostsStore } from '@/stores/HostsStore'
    import { useErrorStore } from '@/stores/ErrorStore'
    
    import SectionEditor from './SectionEditor.vue'
    import HostTagsInput from './HostTagsInput.vue'
    import Confirm from './Confirm.vue'

    export default {
        name: "HostEditor",
        components: {
            'section-editor': SectionEditor,
            'confirm': Confirm,
            'tags-input': HostTagsInput
        },
        props: ['id'],
        data() {
            return {
                title: "",
                configs: {
                    http: {},
                    https: {}
                },
                newConfig: {
                    directive: '',
                    value: ''
                },
                tags: [],
                message: '',
            }
        },
        computed: {
            ...mapState(useHostsStore, {
                host: 'currentHost',
                isLoading: 'isLoading',
                isSaving: 'isSaving',
                isDeleting: 'isDeleting',
                fieldErrors: 'errors',
            }),
            ...mapState(useErrorStore, {
                errors: 'errors',
            }),
            editorTitle() {
                if (this.id > 0) {
                    return this.title
                } else {
                    return this.host.domain
                }
            }
        },
        watch: {
            id() {
                this.loadCurrentHost()
            }
        },
        mounted() {
            this.loadCurrentHost()
        },
        methods: {
            ...mapActions(useHostsStore, ['setCurrentHost', 'updateHost', 'resetCurrentHost', 'addHost', 'getHostsList', 'deleteHost']),
            ...mapActions(useErrorStore, ['addErrors', 'fieldHasError', 'getFieldError', 'clearErrors']),
            loadCurrentHost() {
                if (this.id > 0) {
                    this.setCurrentHost(this.id).then(() => {
                        this.title = JSON.parse(JSON.stringify(this.host.domain));
                        
                        this.configs = { http: {}, https: {} }
                        for (let entry of this.host.configs) {
                            this.configs[entry.section][entry.directive] = entry.value
                        }
                        this.ensureFormVisible()
                    })
                } else {
                    this.configs = {
                        http: {},
                        https: {}
                    }
                    this.tags = {}
                    this.resetCurrentHost()
                    this.ensureFormVisible()
                }
                
            },

            saveHost() {
                this.clearErrors()

                let data = {
                    domain: this.host.domain,
                    config: this.configs,
                    tags: this.host.tags.map(function(tag) {return tag.id;})

                }

                if (this.id > 0) {
                    this.updateHost({id: this.id, host: data})
                        .then(() => {
                            this.message = 'Virtual Host updated.'
                            window.setTimeout(() => {
                                this.message = ''
                            }, 3000)
                        })
                        .catch(err => {
                            this.addErrors(this.fieldErrors)
                        })
                } else {
                    this.addHost(data)
                        .then(resp => {
                            let id = resp.data.data.id
                            this.message = 'Virtual Host created.'
                            window.setTimeout(() => {
                                this.message = ''
                                this.getHostsList()
                                this.$router.push({name: 'host_edit', params: {id: id}})
                            }, 1000)
                            

                        })
                }
                
            },

            confirmDeletion() {
                this.$refs.delConfirm.show()
            },

            deleteCurrentHost() {
                
                this.deleteHost(this.id)
                    .then(() => {
                        this.message = 'Virtual Host deleted. reloading...'
                        window.setTimeout(() => {
                            this.message = ''
                            this.getHostsList()
                            this.$router.push({name: 'home'})
                        }, 1000)
                        
                    })
            },

            ensureFormVisible() {
                const rect = document.getElementById('domain').getBoundingClientRect()
                let isInViewport = rect.top >= 0 &&
                    rect.left >= 0 &&
                    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                    rect.right <= (window.innerWidth || document.documentElement.clientWidth)
                if (!isInViewport) {
                    document.getElementById('hostForm').scrollIntoView()
                }
            }

        }
    }
</script>
