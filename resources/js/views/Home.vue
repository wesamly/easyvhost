<template>
    
    <div class="row">
        <div class="col-12 col-md-6">
            <hosts-filter v-model="filter" :pre-tag-id="tagId"></hosts-filter>
            <div class="table-responsive">
            <table class="table table-hover table-fixed">
                <thead>
                    <tr>
                        <th class="col-4">Domain (Title)</th>
                        <th class="col-1">&nbsp;</th>
                        <th class="col-6">Document Root</th>
                        <th class="col-1">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="host in hostsList" :key="host.id"  :class="{'table-primary': currentHost.id == host.id}">
                        <td class="col-4"><a :href="getHostUrl(host)" target="_blank">{{ host.domain }}</a></td>
                        <td class="col-1">
                            <span v-if="host.doc_root_exists"><i class="bi bi-check-circle text-success"></i></span>
                            <span v-else><i class="bi bi-exclamation-circle text-danger"></i></span>
                        </td>
                        <td class="col-6"><span>{{ getHostConfig(host.configs, 'DocumentRoot') }}</span></td>
                        <td class="col-1">
                            <router-link :to="{name: 'host_edit', params: {id: host.id}}">
                                <i class="bi bi-file-text" v-if="currentHost.id != host.id"></i>
                                <i class="bi bi-file-text-fill" v-else></i>
                            </router-link>
                        </td>
                    </tr>
                    <tr><td class="col-12" colspan="4" v-if="isLoading">Loading...</td></tr>
                    <tr><td class="col-12" colspan="4" v-if="!isLoading && hosts.length == 0">No Records</td></tr>
                </tbody>
            </table>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <router-view></router-view>
        </div>
    </div>
    
</template>

<script>
    import { mapState, mapActions } from 'pinia'
    import { useHostsStore } from '@/stores/HostsStore'
    import HostsFilter from '@/components/HostsFilter.vue'

    export default {
        name: 'Home',
        components: {
            'hosts-filter': HostsFilter
        },
        data() {
            return {
                listHeightSet: false,
                filter: {type: 'host', value: ''},
                tagId: null
            }
        },
        computed: {
            ...mapState(useHostsStore, {
                hosts: 'hosts',
                currentHost: 'currentHost',
                isLoading: 'isLoading'
            }),
            pageIndex() {
                return 0;
            },
            hostsList() {
                let list = [];
                for (let i in this.hosts) {
                    let host = this.hosts[i];
                    if (!this.isFiltered(host)) {
                        continue;
                    }
                    list.push(host);
                }
                return list;
            },
            filterTagIds() {
                let ids = []
                if (this.filter.type == 'tags') {
                    for (let i in this.filter.value) {
                        ids.push(this.filter.value[i].id)
                    }
                }
                return ids
            }
        },
        mounted() {
            let tagId = this.$route.query.tag_id
            if (tagId) {
                this.tagId = Number(tagId)
            }
            this.fetchHosts()
        },
        methods: {
            ...mapActions(useHostsStore, ['getHostsList']),
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
                    if (entry.directive == key) {
                        return entry.value
                    }
                }
                return ''
            },
            getHostUrl(host) {
                let serverName = this.getHostConfig(host.configs, 'ServerName')
                if (String(serverName).indexOf('://') > -1) {
                    return serverName
                }
                let addrPort = this.getHostConfig(host.configs, '_addr_port')
                let scheme = 'http://'
                if (addrPort.indexOf(':443') > -1) {
                    scheme = 'https://'
                }
                
                return `${scheme}${serverName}`
            },
            isFiltered(host) {
                if (this.filter.type == 'host' && this.filter.value != '') {
                    return host.domain.indexOf(this.filter.value) > -1
                }
                if (this.filter.type == 'document' && this.filter.value != '') {
                    return this.getHostConfig(host.configs, 'DocumentRoot').indexOf(this.filter.value) > -1
                }
                if (this.filter.type == 'document_exist' && this.filter.value != '') {
                    let exists = this.filter.value == 'yes'
                    return host.doc_root_exists == exists
                }
                if (this.filter.type == 'tags' && this.filterTagIds.length > 0) {
                    return this.matchingTagFilter(host)
                }
                return true
            },
            matchingTagFilter(host) {
                for (let j in this.filterTagIds) {
                    for (let k in host.tag_ids) {
                        if (parseInt(host.tag_ids[k]) == parseInt(this.filterTagIds[j])) {
                            return true
                        }
                    }
                }
                return false
            }
        }
    }
</script>

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
.table-fixed tbody tr td:nth-child(1) {
    overflow: hidden; 
    white-space: nowrap;
}
.table-fixed tbody tr td:nth-child(3) {
    text-overflow: ellipsis;
    overflow: hidden; 
    white-space: nowrap;
}
</style>