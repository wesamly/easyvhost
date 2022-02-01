<template>
  <div class="row mb-3">
    <div class="col">
      <tags-input
        element-id="tags"
        v-model="myTags"
        :existing-tags="tags"
        id-field="id"
        text-field="name"
        :before-adding-tag="addMissingTag"
        :typeahead="true"
        @tag-added="tagsUpdated"
        @tag-removed="tagsUpdated"
        @tags-updated="tagsUpdated"
      ></tags-input>
    </div>
  </div>
</template>
<script>
import { mapState, mapActions } from "vuex";
import VoerroTagsInput from "@voerro/vue-tagsinput";

export default {
  name: "HostTags",
  components: {
    "tags-input": VoerroTagsInput,
  },
  props: {
    value: {
      type: Array,
      default() {
        return [];
      },
    },
  },
  data() {
    return {
      myTags: [],
    };
  },

  computed: {
    ...mapState("tags", {
      tags: (state) => state.tags,
      isLoading: (state) => state.isLoading,
    }),
  },
  watch: {
    value(newValue) {
      this.myTags = newValue;
    },
  },
  mounted() {
    this.getTags();
  },
  methods: {
    ...mapActions("tags", ["getTags", "addTag"]),
    autoComplete() {
      return new Promise((resolve, reject) => {
        this.getTags()
          .then((resp) => {
            resolve(resp.data);
          })
          .catch((err) => {
            reject(err);
          });
      });
    },
    async addMissingTag(tag) {
      if (tag.id != "") {
        return true;
      }

      //Tag doesn't exist, create it
      await this.addTag({ name: tag.name }).then((resp) => {
        for (let i in this.myTags) {
          if (this.myTags[i].name == tag.name) {
            this.myTags[i].id = resp.data.data.id;
          }
        }
      });

      return true;
    },
    tagsUpdated() {
      this.$emit("input", this.myTags);
    },
  },
};
</script>