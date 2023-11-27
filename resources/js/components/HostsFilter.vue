<template>
    <div :class="['hosts-filter', {'row g-0': isTagsFilter}]">
        <div :class="[{'input-group': !isTagsFilter}, {'col-6': isTagsFilter}]">
            <select class="form-select filter-type" v-model="filter.type" @change="typeUpdated">
                <option value="host">Filter by Domain / ServerName</option>
                <option value="document">Filter by Document Root</option>
                <option value="document_exist">Filter by Document Root Existence</option>
                <option value="tags">Filter by Tags</option>
            </select>
            <input type="text" class="form-control" v-model="query" v-if="hasTextFilter" />
            <button class="btn btn-secondary" type="button" @click="query = ''" v-if="hasTextFilter && query != ''">
                <i class="bi bi-x"></i>
            </button>
            <select class="form-select" v-model="query" v-if="filter.type== 'document_exist'">
                <option value="">All statuses</option>
                <option value="yes">Exists</option>
                <option value="no">Does not exist</option>
            </select>
        </div>
        <div class="multiselect-wrapper col-6" v-if="isTagsFilter">
            <multiselect
                v-model="query"
                :options="tags"
                placeholder="Search by tags"
                :multiple="true"
                label="name"
                track-by="name"
                :hide-selected="true"
                ></multiselect>
        </div>
    </div>
</template>

<script>
import debounce from 'lodash.debounce'
import Multiselect from 'vue-multiselect'
import { mapState, mapActions } from "pinia"
import { useTagsStore } from "@/stores/TagsStore"

export default {
    components: {
        Multiselect
    },
    props: {
        modelValue: {
            type: Object,
            default: () => {
                return {
                    type: '',
                    value: null
                }
            }
        },
        preTagId: {
            type: Number,
            default: null
        }
    },
    emits: ['update:modelValue'],
    data() {
        return {
            query: null,
            textFilters: ['host', 'document'],
        }
    },
    computed: {
        ...mapState(useTagsStore, {
            tags: 'tags'
        }),
        filter: {
            get() {
                this.query = this.modelValue.value
                return this.modelValue
            },
            set: debounce(function(value) {
                this.$emit('update:modelValue', value)
            }, 150)
        },
        hasTextFilter() {
            return this.textFilters.includes(this.filter.type)
        },
        isTagsFilter() {
            return this.filter.type == 'tags'
        }
    },
    watch: {
        query() {
            this.updateFilterValue()
        }
    },
    mounted() {
        this.fetchTags()
    },
    methods: {
        ...mapActions(useTagsStore, ["getTags"]),
        fetchTags() {
            this.getTags()
                .then(() => {
                    if (this.preTagId) {
                        this.filter.type = 'tags'
                        for (let i in this.tags) {
                            if (this.tags[i].id == this.preTagId) {
                                this.filter.value = [this.tags[i]]
                            }
                        }
                    }
                })
        },
        typeUpdated() {
            this.filter.value = null
            if (this.textFilters.includes(this.filter.type) || this.filter.type == 'document_exist') {
                this.filter.value = ''
            }
            if (this.filter.type == 'tags') {
                this.filter.value = []
            }
        },
        updateFilterValue: debounce(function() {
            this.filter.value = this.query
        }, 500)
    }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
