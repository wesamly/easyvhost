<template>
  <div class="row">
      <div class="col">
            <h4> 
                <BootstrapIcon icon="gear-fill" variant="secondary" /> Settings 
                <span class="badge bg-info text-dark" v-if="isLoading">Loading...</span>
            </h4>
            <div class="row">
                <div class="col col-md-7">
                    <div class="alert alert-success" v-if="message != ''">{{ message }}</div>
                </div>
            </div>
            
            <form action="" method="post" @submit.prevent="saveSettings">
                <fieldset>
                    <legend>VirtualHosts Config Files</legend>

                    <config-file class="mb-3" :is-default="true" :key="'_default'" v-model="settings.configs.default"></config-file>
                    <config-file class="mb-3" v-for="(entry, index) in settings.configs.files" :key="index"
                                v-model="settings.configs.files[index]"
                                @delete="deleteFile(index)"
                                ></config-file>
                    <div class="row ">
                        <div class="col col-md-7 text-end">
                            <button class="btn btn-primary" type="button" @click="addFile"><BootstrapIcon icon="plus-circle-fill" /> Add File for Tag</button>
                        </div>
                    </div>            
                </fieldset>

                <fieldset>
                    <legend>VirtualHosts List</legend>
                    <div class="row mb-3">
                        <div class="col-sm-10 offset-sm-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" disabled>
                                <label class="form-check-label" for="flexCheckDefault">
                                    Use Pagination
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="per_page" class="col-sm-2 col-form-label">Per Page</label>
                        <div class="col-sm-5">
                            <input type="number" class="form-control" id="per_page" disabled>
                        </div>
                    </div>
                </fieldset>

                <button type="submit" class="btn btn-primary" :disabled="isLoading || isSaving">Save<span v-if="isSaving">...</span></button>
          </form>
      </div>
  </div>
</template>
<script>

    import { mapState, mapActions } from 'vuex'
    import ConfigFile from './../components/ConfigFile.vue'

    export default {
        name: 'Settings',
        components: {
            'config-file': ConfigFile
        },
        data() {
            return {
                message: '',
            }
        },
        computed: {
            ...mapState('settings', {
                settings: state => state.settings,
                isLoading: state => state.isLoading,
                isSaving: state => state.isSaving
            }),
        },
        mounted() {
            this.getSettings()
        },
        methods: {
            ...mapActions('settings', ['getSettings', 'updateSettings']),
            addFile() {
                this.settings.configs.files.push({tags: [], file: ''})
            },
            deleteFile(index) {
                this.settings.configs.files.splice(index, 1)
            },
            saveSettings() {
                this.updateSettings(this.settings)
                    .then(() => {
                        this.message = 'Settings saved'
                        window.setTimeout(() => {
                            this.message = ''
                        }, 3000)
                    })
            }

        }
    }
</script>

<style scoped>
    h4 span.badge {
        font-size: small;
        font-weight: normal;
    }
</style>