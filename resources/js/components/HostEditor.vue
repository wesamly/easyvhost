<template>
    <div class="">
        <h5>{{ editorTitle }}</h5>

        <form action="" method="post" @submit.prevent="saveHost">
            <div class="row mb-3">
                <label for="domain" class="col-sm-2 col-form-label">Domain</label>
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
                <input type="text" class="form-control" id="ServerName" v-model="configs.ServerName" placeholder="example.com" :readonly="id > 0">
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
                            <BootstrapIcon icon="dash-circle-fill" />
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
                    <div class="col-1"><button class="btn btn-success" type="button" @click="addConfig"><BootstrapIcon icon="plus-circle-fill" /></button></div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Tags</legend>
                
                <host-tags v-model="host.tags"></host-tags>

            </fieldset>
            <button type="submit" class="btn btn-primary"><BootstrapIcon icon="arrow-clockwise" animation="spin" v-if="isSaving" :disabled="isSaving" /> Submit</button>
        </form>
    </div>
</template>

<script>
    import { mapState, mapActions } from 'vuex'
    
    import HostTags from './HostTags.vue'

    export default {
        name: "HostEditor",
        components: {
            'host-tags': HostTags
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
                tags: []
            }
        },
        computed: {
            ...mapState('hosts', {
                host: state => state.currentHost,
                isLoading: state => state.isLoading,
                isSaving: state => state.isSaving,
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
            ...mapActions('hosts', ['setCurrentHost', 'updateHost', 'resetCurrentHost', 'addHost', 'getHostsList']),
            loadCurrentHost() {
                if (this.id > 0) {
                    this.setCurrentHost(this.id).then(() => {
                        this.title = JSON.parse(JSON.stringify(this.host.domain));
                        this.configs = {}
                        for (let i in this.host.configs) {
                            let entry = this.host.configs[i]
                            this.configs[entry.directive] = entry.value
                        }
                    })
                } else {
                    this.configs = {}
                    this.tags = {}
                    this.resetCurrentHost()
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
                        .then(resp => {
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
                
            }
            

        }
    }
</script>