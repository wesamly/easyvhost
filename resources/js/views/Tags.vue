<template>
  <div class="row">
    <div class="col-12 col-md-6">
      <h4><i class="bi bi-bookmarks-fill text-secondary"></i> Tags</h4>
      <div class="form-group">
        <button class="btn btn-success btn-sm" @click="showTagEditor({id: 0, name: ''})">Add</button>
      </div>
      <form class="row">
        <div class="col-2">
          <input type="text" readonly class="form-control-plaintext" value="Filter:" />
        </div>
        <div class="col-10">
          <input type="text" class="form-control" id="tag">
        </div>
        
      </form>
      <table class="table table-hover table-fixed">
        <thead>
          <tr>
            <th>Tag</th>
            <th>Hosts</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="tag in tags" :key="tag.id">
            <td>{{ tag.name }}</td>
            <td>
              <router-link :to="{name: 'home', query: {tag_id: tag.id}}" v-if="tag.hosts_count > 0" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-code"></i> {{ tag.hosts_count }}
              </router-link>
              
              <span v-if="tag.hosts_count == 0">{{ tag.hosts_count }}</span>
            </td>
            <td class="text-end">
              <button class="btn btn-primary btn-sm mx-1" @click="showTagEditor(tag)">Edit</button>
              <button class="btn btn-danger btn-sm" @click="confirmDeletion(tag)">Delete</button>
            </td>
          </tr>
          <tr>
            <td colspan="3" v-if="!isLoading && tags.length == 0">
              No records.
            </td>
          </tr>
          <tr>
            <td colspan="3" v-if="isLoading">Loading...</td>
          </tr>
        </tbody>
      </table>

      <tag-editor ref="tagEditor" @tag-updated="editTag"></tag-editor>
      <confirm ref="delConfirm" @confirmed="removeTag" :content="`This will delete ${delTag.name} tag. Continue?`"></confirm>
    </div>
    <div class="col-12 col-md-6">&nbsp;</div>
  </div>
</template>
<script>
import { mapState, mapActions } from "pinia"
import { useTagsStore } from "@/stores/TagsStore"
import TagEditor from "@/components/TagEditor.vue"
import Confirm from "@/components/Confirm.vue"

export default {
  name: "Tags",
  components: {
    TagEditor, Confirm
  },
  data() {
    return {
      delTag: {}
    };
  },
  computed: {
    ...mapState(useTagsStore, {
      tags: 'tags',
      isLoading: 'isLoading',
    })
  },
  mounted() {
    this.fetchTags()
  },
  methods: {
    ...mapActions(useTagsStore, ["getTags", "addTag", "updateTag", "deleteTag"]),
    
    fetchTags() {
      this.getTags({ include: "hosts_count" }).then(() => {});
    },

    editTag(tag) {
      if (tag.id == 0) {
        this.addTag(tag)
          .then(resp => {
            this.fetchTags()
          })
        
      } else {
        for (let i in this.tags) {
          if (this.tags[i].id == tag.id) {
            let tempTag = this.tags[i]
            tempTag.name = tag.name
            Vue.set(this.tags, i, tempTag)
            break
          }
        }
        this.updateTag({id: tag.id, tag: tag})
          .then(resp => {
            //TODO: message
          })
      }
      
    },
    
    showTagEditor(tag) {
      this.$refs.tagEditor.show(tag)
    },

    confirmDeletion(tag) {
      this.delTag = tag
      this.$refs.delConfirm.show()
    },
    removeTag() {
      if (!this.delTag.id) {
        return
      }
      this.deleteTag(this.delTag.id)
        .then(() => {
          this.fetchTags()
        })
    }
  },
};
</script>