<template>
    <div class="row">
        <div class="col-12 col-sm-4 col-md-2">
            <tags-input v-if="!isDefault" v-model="value.tags"></tags-input>
            <span v-if="isDefault">Default</span>
        </div>
        <div class="col-12 col-sm-8 col-md-5">
            <div class="input-group">
                <input type="text" class="form-control" v-model="value.file" :placeholder="inputPlaceHolder">
                <button class="btn btn-danger" v-if="!isDefault" @click="deleteFile" type="button">
                    <i class="bi bi-dash-circle-fill"></i>
                </button>
            </div>
        </div>
    </div>
</template>
<script>

import HostTagsInput from './HostTagsInput.vue'

export default {
    name: 'ConfigFile',
    components: {
        'tags-input': HostTagsInput
    },
    props: {
        isDefault: {
            type: Boolean,
            default: false
        },
        modelValue: {
            type: Object,
            default: () => ({
                tags: [],
                path: ''
            })
        }
    },
    emits: ['update:modelValue'],
    data() {
        return {}
    },
    computed: {
        inputPlaceHolder() {
            return this.isDefault ? 'E.g. /usr/local/etc/httpd/extra/httpd-vhosts-default.conf' : 'E.g. /usr/local/etc/httpd/extra/httpd-vhosts-example-tag.conf'
        },
        value: {
            get() {
                return this.modelValue
            },
            set(value) {
                this.$emit('update:modelValue', value)
            }
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