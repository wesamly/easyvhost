<template>
    <div class="card mb-3">
        <div class="card-header text-white2 d-flex align-items-center"
            :class="{ 'text-bg-secondary': section == 'https' }">
            <span class="me-auto" v-if="section == 'http'">HTTP</span>
            <span class="me-auto" v-if="section == 'https'">HTTPS (SSL)</span>
            <div class="form-check me-3" v-if="section == 'https'">
                <input class="form-check-input" type="checkbox" id="enableSSL" v-model="configs.SSLEngine"
                    true-value="On" false-value="">
                <label class="form-check-label" for="enableSSL">
                    Enable SSL
                </label>
            </div>
            <div class="w-50 row" v-if="showControls">
                <label :for="`${section}_addr_port`" class="col-sm-4 col-form-label">addr[:port]</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control form-control-sm" :id="`${section}_addr_port`"
                        :placeholder="addrPortPlaceholder" v-model="configs._addr_port">
                </div>
            </div>
        </div>
        <div class="card-body" v-if="showControls">
            <div class="form-check mb-3" v-if="section == 'https'">
                <input class="form-check-input" type="checkbox" id="shareDirectives" v-model="configs._share_directives"
                    true-value="1" false-value="0">
                <label class="form-check-label" for="shareDirectives">
                    Share directives from HTTP section. You can override by adding same directive in this section.
                </label>
            </div>
            <div class="row mb-3" v-for="(entry, index) in mainDirectives" :key="`md-${index}`">
                <label :for="`${section}_${entry}`" class="col-sm-3 col-form-label">{{ entry }}</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control form-control-sm" :id="`${section}_${entry}`"
                        :placeholder="directives[entry]" v-model="configs[entry]">
                </div>
            </div>
            <div class="row mb-3" v-for="(value, directive) in otherConfigs" :key="directive">
                <label :for="`${section}_${directive}`" class="col-sm-3 col-form-label">{{ directive }}</label>
                <div class="col-sm-9">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" :id="`${section}_${directive}`"
                            v-model="configs[directive]" :placeholder="directives[directive]">
                        <button class="btn btn-danger btn-sm" type="button" @click="removeConfig(directive)">
                            <i class="bi bi-dash-circle-fill"></i>
                        </button>
                    </div>
                </div>
            </div>

            <fieldset class="mb-1">
                <legend class="fs-5">Add Directive</legend>
                <div class="row">
                    <div class="col-3">
                        <input type="text" class="form-control form-control-sm" :list="`${section}-directives-list`"
                            v-model="newConfig.directive" @change="updateNewConfigPlaceholder">
                        <datalist :id="`${section}-directives-list`">
                            <option v-for="item in availableDirectives" :value="item" :key="item" />
                        </datalist>
                    </div>
                    <div class="col-8"> <input type="text" class="form-control form-control-sm"
                            v-model="newConfig.value" :placeholder="newConfig.placeholder" /></div>
                    <div class="col-1">
                        <button class="btn btn-success btn-sm float-end" type="button" @click="addConfig">
                            <i class="bi bi-plus-circle-fill"></i>
                        </button>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</template>
<script>

export default {
    name: "SectionEditor",
    emits: ['update:modelValue'],
    props: {
        section: {
            type: String,
            default: 'http',
            validator: function (value) {
                return ['http', 'https'].indexOf(value) !== -1
            }
        },
        modelValue: {
            type: Object
        }
    },
    data() {
        return {
            newConfig: {
                directive: '',
                value: '',
                placeholder: ''
            }
        }
    },
    computed: {
        showControls() {
            return this.section === 'http' || (this.section === 'https' && this.configs.SSLEngine)
        },
        addrPortPlaceholder() {
            return AppVars.addrPorts[this.section]
            //return this.section === 'https' ? '*:443' : '*:80'
        },
        directives() {
            let list = AppVars.directives.list
            if (this.section === 'https') {
                list = { ...list, ...AppVars.directives.https }
            }
            return list
        },
        mainDirectives() {
            let list = []
            if (this.section === 'http') {
                list = AppVars.directives.main
            }
            return list
        },
        availableDirectives() {
            let list = []
            let configKeys = Object.keys(this.configs)
            for (let i in this.directives) {
                if (!this.mainDirectives.includes(i) && configKeys.indexOf(i) == -1) {
                    list.push(i)
                }
            }
            return list
        },
        otherConfigs() {
            let filtered = {}
            for (let directive in this.configs) {
                if (this.mainDirectives.indexOf(directive) == -1 && directive.substring(0, 1) != '_'
                    && ['addr[:port]', 'SSLEngine'].indexOf(directive) == -1) {
                    filtered[directive] = this.configs[directive]
                }
            }
            return filtered
        },
        configs: {
            get() {
                return this.modelValue[this.section] || {}
            },
            set(value) {
                this.$emit('update:modelValue', value)
            }
        }
    },
    watch: {
        'configs': {
            handler() {
                if (this.section === 'https' && this.configs.hasOwnProperty('SSLEngine')
                    && this.configs.SSLEngine == 'On' && !this.configs.hasOwnProperty('_share_directives')) {
                    this.configs._share_directives = 1
                }
            },
            deep: true
        }
    },
    methods: {
        updateNewConfigPlaceholder() {
            this.newConfig.placeholder = this.directives[this.newConfig.directive] ?? ''
        },
        addConfig() {
            for (let i in this.configs) {
                if (i == this.newConfig.directive) {
                    //TODO: show error
                    return false
                }
            }

            this.configs[this.newConfig.directive] = this.newConfig.value

            this.newConfig.directive = ''
            this.newConfig.value = ''
            this.newConfig.placeholder = ''
        },
        removeConfig(directive) {
            delete this.configs[directive]
        },
    }
}
</script>

<style scoped>
.col-form-label {
    padding-bottom: 0 !important;
}
</style>