<template>
    <div class="input-group mb-3">
        <input type="text" class="form-control form-control-sm" :id="`${section}_${entry}`"
                        :placeholder="directives[entry]" v-model="configs[entry]">
        <span class="input-group-text">
            <i :class="pathExistClass"></i>
        </span>
        <a v-if="canOpenDir" class="btn btn-outline-primary" :href="cleanPath" target="_blank">
            <i class="bi bi-folder"></i>
        </a>
    </div>
</template>
<script>
export default {
    props: {
        host: Object,
        section: String,
        entry: String,
        directives: Object,
        configs: Object
    },
    computed: {
        pathExistClass() {
            return this.host.doc_root_exists ? 'bi bi-check-circle text-success' : 'bi bi-exclamation-circle text-danger'
        },
        canOpenDir() {
            return this.host.doc_root_exists && AppVars.openDirProtocol
        },
        cleanPath() {
            let dir = this.configs[this.entry]
            if (dir == null) return
            // Remove " from start and end
            dir = dir.substring(1, dir.length - 1)
            return AppVars.openDirProtocol.replace('%path%', dir)
        }
    },
    methods: {
        openFolder() {
            let dir = this.configs[this.entry]
            // Remove " from start and end
            dir = dir.substring(1, dir.length - 1)

            window.open('easyvhost://' + dir, '_blank')
        }
    }
}
</script>