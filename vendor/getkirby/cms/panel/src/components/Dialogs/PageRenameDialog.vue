<template>
  <k-dialog
    ref="dialog"
    :button="$t('rename')"
    size="medium"
    theme="positive"
    @submit="$refs.form.submit()"
  >
    <k-form
      ref="form"
      :fields="fields"
      v-model="page"
      @submit="submit"
    />
  </k-dialog>
</template>

<script>
import DialogMixin from "@/mixins/dialog.js";

export default {
  mixins: [DialogMixin],
  data() {
    return {
      page: {
        id: null,
        title: null
      }
    };
  },
  computed: {
    fields() {
      return {
        title: {
          label: this.$t("page.title"),
          type: "text",
          required: true,
          icon: "title",
          preselect: true
        }
      };
    }
  },
  methods: {
    open(id) {
      this.$api.pages.get(id, { select: ["id", "title"] })
        .then(page => {
          this.page = page;
          this.$refs.dialog.open();
        })
        .catch(error => {
          this.$store.dispatch('notification/error', error);
        });
    },
    submit() {

      if (this.page.title.length === 0) {
        this.$refs.dialog.error(this.$t("error.page.changeTitle.empty"));
        return;
      }

      this.$api.pages
        .title(this.page.id, this.page.title)
        .then(() => {
          this.success({
            message: this.$t("page.renamed"),
            event: "page.changeTitle"
          });
        })
        .catch(error => {
          this.$refs.dialog.error(error.message);
        });
    }
  }
};
</script>
