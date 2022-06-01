<template>
    <div class="row">
        <div class="col-12 col-sm-4 col-md-2">
            <host-tags v-if="!isDefault" v-model="value.tags"></host-tags>
            <span v-if="isDefault">Default</span>
        </div>
        <div class="col-12 col-sm-8 col-md-5">
            <div class="input-group">
                <input type="text" class="form-control" v-model="value.file" :placeholder="inputPlaceHolder">
                <button class="btn btn-danger" v-if="!isDefault" @click="deleteFile" type="button">
                    <BootstrapIcon icon="dash-circle-fill" />
                </button>
            </div>
        </div>
    </div>
</template>
<script>

import HostTags from './HostTags.vue'

export default {
    name: 'ConfigFile',
    components: {
        'host-tags': HostTags
    },
    props: {
        isDefault: {
            type: Boolean,
            default: false
        },
        value: {
            type: Object,
            default: () => ({
                tags: [],
                path: ''
            })
        }
    },
    data() {
        return {}
    },
    computed: {
        inputPlaceHolder() {
            return this.isDefault ? 'E.g. /usr/local/etc/httpd/extra/httpd-vhosts-default.conf' : 'E.g. /usr/local/etc/httpd/extra/httpd-vhosts-example-tag.conf'
        }
    },
    methods: {
        deleteFile() {
            this.$emit('delete')
        }
    }
}
</script>

<style scoped>
    .mb-3 .mb-3 {
        margin-bottom: 0 !important;
    }
</style>