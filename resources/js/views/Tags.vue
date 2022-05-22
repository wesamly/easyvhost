<template>
  <div class="row">
    <div class="col">
      <h4><BootstrapIcon icon="bookmarks-fill" variant="secondary" /> Tags</h4>
      <div class="form-group">
        <button class="btn btn-success btn-sm" @click="showTagEditor({id: 0, name: ''})">Add</button>
      </div>
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
              <a href="#" v-if="tag.hosts_count > 0">{{ tag.hosts_count }}</a>
              <span v-if="tag.hosts_count == 0">{{ tag.hosts_count }}</span>
            </td>
            <td class="text-end">
              <button class="btn btn-primary btn-sm" @click="showTagEditor(tag)">Edit</button>
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
    <div class="col">&nbsp;</div>
  </div>
</template>
<script>
import { mapState, mapActions } from "vuex";
import TagEditor from "../components/TagEditor"
import Confirm from "../components/Confirm.vue"

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
    ...mapState("tags", {
      tags: (state) => state.tags,
      isLoading: (state) => state.isLoading,
    })
  },
  mounted() {
    this.fetchTags()
  },
  methods: {
    ...mapActions("tags", ["getTags", "addTag", "updateTag", "deleteTag"]),
    
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