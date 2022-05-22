<template>
  <div class="modal" tabindex="-1" id="editorModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <span v-if="id > 0">Edit Tag <strong>{{ oldName }}</strong></span>
            <span v-else>Add Tag</span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="form-group">
              <label for="tag-name">Tag Name</label>
              <input type="text" class="form-control" id="tag-name" v-model="name" placeholder="Tag Name">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="setResult('canceled')">Cancel</button>
          <button type="button" class="btn btn-primary" @click="setResult('updated')">Save</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { Modal } from 'bootstrap';

export default {
  name: "TagEditor",
  data() {
    return {
      id: 0,
      name: '',
      oldName: '',
    };
  },
  methods: {
    show(tag) {
      this.id = tag.id
      this.name = tag.name
      this.oldName = ''
      if (this.id > 0) {
        this.oldName = JSON.parse(JSON.stringify(this.name))
      }
      this.modal = new Modal(document.getElementById('editorModal'), {
          keyboard: false
      })
      this.modal.show()
    },
    setResult(result) {
        this.modal.hide()
        if (result == 'updated') {
            this.$emit('tag-updated', {
                id: this.id,
                name: this.name,
            })
        }
        if (result == 'canceled') {
            this.$emit('canceled')
        }
    },
  },
};
</script>