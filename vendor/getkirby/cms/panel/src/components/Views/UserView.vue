<template>
  <k-error-view v-if="issue">
    {{ issue.message }}
  </k-error-view>
  <div v-else-if="ready" class="k-user-view">

    <div class="k-user-profile">
      <k-view>
        <template v-if="avatar">
          <k-dropdown>
            <k-button class="k-user-view-image" @click="$refs.picture.toggle()">
              <k-image
                v-if="avatar"
                :cover="true"
                :src="avatar"
                ratio="1/1"
              />
            </k-button>
            <k-dropdown-content ref="picture">
              <k-dropdown-item icon="upload" @click="$refs.upload.open()">
                {{ $t('change') }}
              </k-dropdown-item>
              <k-dropdown-item icon="trash" @click="action('picture.delete')">
                {{ $t('delete') }}
              </k-dropdown-item>
            </k-dropdown-content>
          </k-dropdown>
        </template>
        <template v-else>
          <k-button class="k-user-view-image" @click="$refs.upload.open()">
            <k-icon type="user" />
          </k-button>
        </template>

        <k-button-group>
          <k-button :disabled="!permissions.changeEmail" icon="email" @click="action('email')">{{ $t('user.email') }}: {{ user.email }}</k-button>
          <k-button :disabled="!permissions.changeRole" icon="bolt" @click="action('role')">{{ $t('user.role') }}: {{ user.role.title }}</k-button>
          <k-button :disabled="!permissions.changeLanguage" icon="globe" @click="action('language')">{{ $t('user.language') }}: {{ user.language }}</k-button>
        </k-button-group>
      </k-view>
    </div>

    <k-view>

      <k-header
        :editable="permissions.changeName"
        :tabs="tabs"
        :tab="tab"
        @edit="action('rename')"
      >
        <span v-if="!user.name || user.name.length === 0" class="k-user-name-placeholder">{{ $t('user.name') }} …</span>
        <template v-else>{{ user.name }}</template>
        <k-button-group slot="left">
          <k-dropdown>
            <k-button icon="cog" @click="$refs.settings.toggle()">
              {{ $t('settings') }}
            </k-button>
            <k-dropdown-content ref="settings" :options="options" @action="action" />
          </k-dropdown>
        </k-button-group>
        <k-button-group v-if="user.id && $route.name === 'User'" slot="right">
          <k-button :disabled="!prev" v-bind="prev" icon="angle-left" />
          <k-button :disabled="!next" v-bind="next" icon="angle-right" />
        </k-button-group>
      </k-header>

      <k-tabs
        v-if="user && tabs.length"
        ref="tabs"
        :key="'user-' + user.id + '-tabs-' + new Date().getTime()"
        :parent="'users/' + user.id"
        :blueprint="user.blueprint.name"
        :tabs="tabs"
        @tab="tab = $event"
      />

      <k-box v-else-if="ready" :text="$t('user.blueprint', { role: user.role.name })" theme="info" />

      <k-user-email-dialog ref="email" />
      <k-user-language-dialog ref="language" @success="fetch" />
      <k-user-password-dialog ref="password" />
      <k-user-remove-dialog ref="remove" />
      <k-user-rename-dialog ref="rename" @success="fetch" />
      <k-user-role-dialog ref="role" @success="fetch" />

      <k-upload
        ref="upload"
        :url="uploadApi"
        :multiple="false"
        accept="image/jpeg"
        @success="uploadedAvatar"
      />
    </k-view>
  </div>

</template>

<script>
import PrevNext from "@/mixins/prevnext.js";
import config from "@/config/config.js";

export default {
  mixins: [PrevNext],
  props: {
    id: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      tab: null,
      tabs: [],
      ready: false,
      user: {
        role: {
          name: null
        },
        name: null,
        language: null,
        prev: null,
        next: null
      },
      permissions: {
        changeEmail: true,
        changeName: true,
        changeLanguage: true,
        changeRole: true
      },
      issue: null,
      avatar: null,
      options: null
    };
  },
  computed: {
    prev() {
      if (this.user.prev) {
        return {
          link: this.$api.users.link(this.user.prev.id),
          tooltip: this.user.prev.name
        };
      }
    },
    next() {
      if (this.user.next) {
        return {
          link: this.$api.users.link(this.user.next.id),
          tooltip: this.user.next.name
        };
      }
    },
    uploadApi() {
      return config.api + "/users/" + this.user.id + "/avatar";
    }
  },
  methods: {
    action(action) {
      switch (action) {
        case "email":
          this.$refs.email.open(this.user.id);
          break;
        case "language":
          this.$refs.language.open(this.user.id);
          break;
        case "password":
          this.$refs.password.open(this.user.id);
          break;
        case "picture.delete":
          this.$api.users.deleteAvatar(this.id).then(() => {
            this.$store.dispatch(
              "notification/success",
              this.$t("user.avatar.deleted")
            );
            this.avatar = null;
          });
          break;
        case "remove":
          this.$refs.remove.open(this.user.id);
          break;
        case "rename":
          this.$refs.rename.open(this.user.id);
          break;
        case "role":
          this.$refs.role.open(this.user.id);
          break;
        default:
          this.$store.dispatch("notification/error", "Not yet implemented");
      }
    },
    fetch() {
      this.$api.users
        .get(this.id, { view: "panel" })
        .then(user => {
          this.user = user;
          this.tabs = user.blueprint.tabs;
          this.ready = true;
          this.permissions = user.options;
          this.options = ready => {
            this.$api.users.options(this.user.id).then(options => {
              ready(options);
            });
          };

          if (user.avatar.exists) {
            this.avatar = user.avatar.url + "?v=" + user.avatar.modified;
          } else {
            this.avatar = null;
          }

          if (this.$route.name === "User") {
            this.$store.dispatch("breadcrumb", this.$api.users.breadcrumb(user));
          } else {
            this.$store.dispatch("breadcrumb", []);
          }

          this.$store.dispatch("title", this.user.name || this.user.email);
        })
        .catch(error => {
          this.issue = error;
        });
    },
    uploadedAvatar() {
      this.$store.dispatch(
        "notification/success",
        this.$t("user.avatar.uploaded")
      );
      this.fetch();
    }
  }
};
</script>

<style lang="scss">

.k-user-profile {
  background: $color-white;
}
.k-user-profile > .k-view {
  padding-top: 3rem;
  padding-bottom: 3rem;
  display: flex;
  align-items: center;
  line-height: 0;
}
.k-user-profile .k-button-group {
  margin-left: .75rem;
  overflow: hidden;
}
.k-user-profile .k-button-group .k-button {
  display: block;
  padding-top: .25rem;
  padding-bottom: .25rem;
  overflow: hidden;
  white-space: nowrap;
}
.k-user-profile .k-button-group .k-button[disabled] {
  opacity: 1;
}

.k-user-profile .k-dropdown-content {
  margin-top: .5rem;
  left: 50%;
  transform: translateX(-50%);
}
.k-user-view-image .k-image {
  width: 4rem;
  height: 4rem;
  line-height: 0;
}
.k-user-view-image .k-icon {
  width: 4rem;
  height: 4rem;
  background: $color-dark;
  color: $color-light-grey;
}

.k-user-name-placeholder {
  color: $color-light-grey;
  transition: color .3s;
}
.k-header[data-editable] .k-user-name-placeholder:hover {
  color: $color-dark;
}


</style>
