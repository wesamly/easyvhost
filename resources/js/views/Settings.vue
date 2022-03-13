<template>
  <div class="row">
      <div class="col">
            <h4> <BootstrapIcon icon="gear-fill" variant="secondary" /> Settings</h4>
            <form>
                <fieldset>
                    <legend>VirtualHosts Config Files</legend>

                    <div class="row mb-3">
                        <label for="default_file" class="col-sm-2 col-form-label">Default</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="default_file" placeholder="E.g. /usr/local/etc/httpd/extra/httpd-vhosts-default.conf">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-2">
                            <host-tags v-model="settings.configs.files"></host-tags>
                        </div>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" placeholder="E.g. /usr/local/etc/httpd/extra/httpd-vhosts-example-tag.conf">
                        </div>

                    </div>

                </fieldset>

                <fieldset>
                    <legend>VirtualHosts List</legend>
                    <div class="row mb-3">
                        <div class="col-sm-10 offset-sm-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Use Pagination
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="per_page" class="col-sm-2 col-form-label">Per Page</label>
                        <div class="col-sm-5">
                            <input type="number" class="form-control" id="per_page">
                        </div>
                    </div>
                </fieldset>

                <button type="submit" class="btn btn-primary">Save</button>
          </form>
      </div>
  </div>
</template>
<script>
    import HostTags from './../components/HostTags.vue'

    export default {
        name: 'Settings',
        components: {
            'host-tags': HostTags
        },
        data() {
            return {
                settings: {
                    configs: {
                        files: []
                    }
                }
            }
        }
    }
</script>