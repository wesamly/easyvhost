import Vue from 'vue'
import Vuex from 'vuex'

import HostsModule from './hosts'
import TagsModule from './tags'
import SettingsModule from './settings'

Vue.use(Vuex)

const state = {
    globalState: '',
    
}

export default new Vuex.Store({
    state: state,
    modules: {
        hosts: HostsModule,
        tags: TagsModule,
        settings: SettingsModule,
    }
})