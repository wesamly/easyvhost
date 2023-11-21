<template>
    <div class="tag-input-root">
        <div class="tag-input">
            <input v-model="newTag" type="text" list="tagsList" autocomplete="off"
                @keydown.enter.prevent="appendTag(newTag)" @keydown.prevent.tab="appendTag(newTag)"
                @keydown.delete="newTag.length || removeTag(tags.length - 1)"
                :style="{ 'padding-left': `${paddingLeft}px` }"
                placeholder="Add a tag" class="form-control" />

            <ul class="tags" ref="tagsUl">
                <li v-for="tag in myTags" :key="tag.id" class="tag badge round-pill text-bg-primary">
                    {{ tag.name }}
                    <button class="tag-action" @click="removeTag(tag)">x</button>
                </li>
            </ul>

        </div>
        <ul class="existing-tags mt-2" v-if="matchingTags.length > 0">
            <li v-for="tag in matchingTags" :key="tag.id" class="tag badge round-pill text-bg-success">
                {{ tag.name }}
                <button class="tag-action" @click="appendTag(tag)">+</button>
            </li>
        </ul>

    </div>
</template>
<script>
// Modified version of: 
// https://vueschool.io/articles/vuejs-tutorials/building-a-tag-input-component-with-the-vue-3-composition-api/

import { mapState, mapActions } from "pinia";
import { useTagsStore } from "@/stores/TagsStore";

export default {
    props: {
        modelValue: { type: Array, default: () => [] }
    },
    emits: ['update:modelValue'],
    data() {
        return {
            newTag: "",
            paddingLeft: 10,
        }
    },
    computed: {
        ...mapState(useTagsStore, {
            tags: 'tags',
            isLoading: 'isLoading',
        }),
        myTags: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit("update:modelValue", value);
            },
        },
        matchingTags() {
            if (!this.newTag) {
                return [];
            }

            let tags = []
            // Remove current tags
            let myIds = this.myTags.map((tag) => {
                return tag.id
            })
            for (let i in this.tags) {

                if (!myIds.includes(this.tags[i].id)) {
                    tags.push(this.tags[i])
                }
            }
            // Include matching tags
            let list = []
            for (let i in tags) {
                if (tags[i].name.toLowerCase().includes(this.newTag.toLowerCase())) {
                    list.push(tags[i])
                }
            }
            return list
        }
    },

    watch: {
        myTags: {
            deep: true,
            handler() {
                this.$nextTick(this.onTagsChange);
            }
        }
    },
    mounted() {
        this.getTags();
        this.onTagsChange();
    },
    methods: {
        ...mapActions(useTagsStore, ["getTags", "addTag"]),
        appendTag(tag) {
            if (!tag) {
                return
            }
            // Selected a tag
            if (typeof tag == 'object') {
                this.myTags.push(tag)
                this.newTag = ""
                return
            }

            // Add existing tag
            for (let i in this.matchingTags) {
                if (this.matchingTags[i].name.toLowerCase() == tag.toLowerCase()) {
                    this.myTags.push(this.matchingTags[i])
                    this.newTag = ""
                    return
                }
            }

            this.addMissingTag(tag)
        },
        onTagsChange() {
            // Position cursor
            this.paddingLeft = this.$refs.tagsUl.clientWidth + 15;
            // Scroll to end of tags
            this.$refs.tagsUl.scrollTo(this.$refs.tagsUl.scrollWidth, 0);
        },
        async addMissingTag(tag) {
            //Tag doesn't exist, create it
            await this.addTag({ name: tag }).then((resp) => {
                this.myTags.push({
                    id: resp.data.data.id,
                    name: tag
                })
                this.newTag = ""
            });
        },
        removeTag(tag) {
            this.myTags = this.myTags.filter((t) => {
                return t.id != tag.id
            })
        }
    },

};
</script>
<style scoped>
.tag-input {
    position: relative;
}

ul {
    list-style: none;
    display: flex;
    align-items: center;
    gap: 7px;
    margin: 0;
    padding: 0;

    max-width: 75%;
    overflow-x: auto;
}

.tags {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 10px;
}

.tag {
    white-space: nowrap;
}

input {
    width: 100%;
    padding: 10px;
}

.tag-action {
    color: white;
    background: none;
    outline: none;
    border: none;
    cursor: pointer;
}
</style>