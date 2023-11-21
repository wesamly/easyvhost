<template>
    <div class="">
        <h5>{{ editorTitle }}</h5>

        <form action="" method="post" @submit.prevent="saveHost" id="hostForm">
            <div class="row mb-3">
                <label for="domain" class="col-sm-2 col-form-label">Domain (Title)</label>
                <div class="col-sm-10">
                <input type="text" class="form-control" id="domain" v-model="host.domain" placeholder="example.com">
                </div>
            </div>
            <div class="row mb-3">
                <label for="_addr_port" class="col-sm-2 col-form-label">addr[:port]</label>
                <div class="col-sm-10">
                <input type="text" class="form-control" id="_addr_port" v-model="configs._addr_port" placeholder="*:80">
                </div>
            </div>
            <div class="row mb-3">
                <label for="ServerName" class="col-sm-2 col-form-label">ServerName</label>
                <div class="col-sm-10">
                <input type="text" class="form-control" id="ServerName" v-model="configs.ServerName" placeholder="example.com">
                </div>
            </div>
            <div class="row mb-3">
                <label for="DocumentRoot" class="col-sm-2 col-form-label">DocumentRoot</label>
                <div class="col-sm-10">
                <input type="text" class="form-control" id="DocumentRoot" v-model="configs.DocumentRoot" placeholder='"/var/www/example"'>
                </div>
            </div>
            <div class="row mb-3" v-for="(value, directive) in otherConfigs" :key="directive">
                <label :for="directive" class="col-sm-2 col-form-label">{{ directive }}</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <input type="text" class="form-control" :id="directive" v-model="configs[directive]">
                        <button class="btn btn-danger" type="button" @click="removeConfig(directive)">
                            <i class="bi bi-dash-circle-fill"></i>
                        </button>
                    </div>
                </div>
            </div>
            <hr>
            <fieldset class="mb-3">
                <legend>Add Directive</legend>
                <div class="row">
                    <div class="col-4">
                        <input type="text" class="form-control" list="ice-cream-flavors" v-model="newConfig.directive">
                        <datalist id="ice-cream-flavors">
                            <option value="ServerAdmin" />
                            <option value="ErrorLog" />
                            <option value="CustomLog" />
                            <option value="TransferLog" />
                            <option value="ServerAlias" />
                        </datalist>
                    </div>
                    <div class="col-7"> <input type="text" class="form-control" v-model="newConfig.value" /></div>
                    <div class="col-1"><button class="btn btn-success" type="button" @click="addConfig"><i class="bi bi-plus-circle-fill"></i></button></div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Tags</legend>
                
                <tags-input v-model="host.tags" class="mb-3"></tags-input>
            </fieldset>

            <div class="alert alert-success" v-if="message != ''">{{ message }}</div>

            <button type="submit" class="btn btn-primary" :disabled="isSaving"><i class="bi bi-arrow-clockwise" animation="spin" v-if="isSaving"></i> Submit</button>
            <button type="button" class="btn btn-danger float-end" @click="confirmDeletion" :disabled="isDeleting"><i class="bi bi-arrow-clockwise" animation="spin" v-if="isDeleting"></i> Delete</button>
        </form>

        <confirm ref="delConfirm" @confirmed="deleteCurrentHost" :content="`This will delete ${host.domain} virtual host. Continue?`"></confirm>
    </div>
</template>

<script>
    import { mapState, mapActions } from 'pinia'
    import { useHostsStore } from '@/stores/HostsStore'
    
    import HostTagsInput from './HostTagsInput.vue'
    import Confirm from './Confirm.vue'

    export default {
        name: "HostEditor",
        components: {
            'confirm': Confirm,
            'tags-input': HostTagsInput
        },
        props: ['id'],
        data() {
            return {
                title: "",
                configs: {

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
            }),
            otherConfigs() {
                let mainKeys = ['_addr_port', 'ServerName', 'DocumentRoot']
                let filtered = {}
                for (let directive in this.configs) {
                    if (mainKeys.indexOf(directive) == -1) {
                        filtered[directive] = this.configs[directive]
                    }
                }
                return filtered
            },
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
            loadCurrentHost() {
                if (this.id > 0) {
                    this.setCurrentHost(this.id).then(() => {
                        this.title = JSON.parse(JSON.stringify(this.host.domain));
                        this.configs = {}
                        for (let i in this.host.configs) {
                            let entry = this.host.configs[i]
                            this.configs[entry.directive] = entry.value
                        }
                        this.ensureFormVisible()
                    })
                } else {
                    this.configs = {}
                    this.tags = {}
                    this.resetCurrentHost()
                    this.ensureFormVisible()
                }
                
            },
            addConfig() {
                for (let i in this.configs) {
                    if (i == this.newConfig.directive) {
                        //TODO: show error
                        return false
                    }  
                }
                Vue.set(this.configs, this.newConfig.directive, this.newConfig.value)

                this.newConfig.directive = ''
                this.newConfig.value = ''
            },
            removeConfig(directive) {
                let temp = {}
                for (let i in this.configs) {
                    if (i != directive) {
                        temp[i] = this.configs[i]
                    }
                }

                this.configs = temp
            },

            saveHost() {
                let data = {
                    domain: this.host.domain,
                    config: this.configs,
                    tags: this.host.tags.map(function(tag) {return tag.id;})

                }

                if (this.id > 0) {
                    this.updateHost({id: this.id, host: data})
                        .then(() => {
                            //TODO: message
                        })
                } else {
                    this.addHost(data)
                        .then(resp => {
                            let id = resp.data.data.id
                            this.getHostsList()
                            this.$router.push({name: 'host_edit', params: {id: id}})

                        })
                }
                
            },

            confirmDeletion() {
                this.$refs.delConfirm.show()
            },

            deleteCurrentHost() {
                console.log('deleting')
                
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
