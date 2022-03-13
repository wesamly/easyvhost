<template>
    
    <div class="row">
        <div class="col">
            <div class="hosts-filter">
                <div class="input-group">
                    <select class="form-select">
                        <option value="host">Filter by Host</option>
                    </select>
                    <input type="text" class="form-control" v-model="query" />
                    <button class="btn btn-secondary" type="button" @click="query = ''" v-if="query != ''">
                            <BootstrapIcon icon="x" />
                        </button>
                </div>
            </div>
            <div class="table-responsive">
            <table class="table table-hover table-fixed">
                <thead>
                    <tr>
                        <th class="col-4">Server Name</th>
                        <th class="col-1">&nbsp;</th>
                        <th class="col-6">Document Root</th>
                        <th class="col-1">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="host in hosts" :key="host.id" 
                        :class="{'table-primary': currentHost.id == host.id}"
                        v-show="query === '' || host.domain.indexOf(query) > -1"
                        >
                        <td class="col-4"><a :href="`http://${host.domain}`" target="_blank">{{ host.domain }}</a></td>
                        <td class="col-1">
                            <span v-if="host.doc_root_exists"><BootstrapIcon icon="check-circle" variant="success" /></span>
                            <span v-else><BootstrapIcon icon="exclamation-circle" variant="danger" /></span>
                        </td>
                        <td class="col-6"><span>{{ getHostConfig(host.configs, 'DocumentRoot') }}</span></td>
                        <td class="col-1"><router-link :to="{name: 'host_edit', params: {id: host.id}}" class="btn2 btn-outline-secondary2 btn-sm2"><BootstrapIcon icon="arrow-right" /></router-link></td>
                    </tr>
                    <tr><td class="col-12" colspan="4" v-if="isLoading">Loading...</td></tr>
                    <tr><td class="col-12" colspan="4" v-if="!isLoading && hosts.length == 0">No Records</td></tr>
                </tbody>
            </table>
            </div>
        </div>
        <div class="col">
            <router-view></router-view>
        </div>
    </div>
    
</template>

<script>
    import { mapState, mapActions } from 'vuex'

    export default {
        name: 'Home',
        data() {
            return {
                listHeightSet: false,
                query: ''
            }
        },
        computed: {
            ...mapState('hosts', {
                hosts: state => state.hosts,
                currentHost: state => state.currentHost,
                isLoading: state => state.isLoading
            }),
            pageIndex() {
                return 0;//(this.pagination.current_page - 1) * this.pagination.per_page
            }
        },
        mounted() {
            this.fetchHosts()
        },
        methods: {
            ...mapActions('hosts', ['getHostsList', 'deleteHost']),
            fetchHosts() {
                this.getHostsList().then(() => {
                    if (!this.listHeightSet) {
                        window.setTimeout(() => {
                            
                            let heightBeforeTable = document.querySelector('.navbar').clientHeight + document.querySelector('.table-fixed thead tr th').clientHeight + document.querySelector('.hosts-filter').clientHeight
                            let remainingHeight = window.innerHeight - heightBeforeTable - 15
                            document.querySelector('.table-fixed tbody').style.height = remainingHeight + 'px'
                            this.listHeightSet = true
                        }, 500)
                    }
                    
                })
            },
            getHostConfig(configs, key) {
                for (let i in configs) {
                    let entry = configs[i]
                    if (entry.directive == 'DocumentRoot') {
                        return entry.value
                    }
                }
                return ''
            }
        }
    }
</script>

<style scoped>


</style>

<style lang="scss" scoped>
.table-fixed tbody {
    height: 300px;
    overflow-y: auto;
    width: 100%;
    border-bottom: 1px solid lightgray;
}

.table-fixed thead,
.table-fixed tbody,
.table-fixed tr,
.table-fixed td,
.table-fixed th {
    display: block;
}

.table-fixed tbody td,
.table-fixed tbody th,
.table-fixed thead > tr > th {
    float: left;
    position: relative;

    &::after {
        content: '';
        clear: both;
        display: block;
    }
}
.table-fixed tbody tr td:nth-child(3) {
    text-overflow: ellipsis;
    overflow: hidden; 
    white-space: nowrap;
}
</style>