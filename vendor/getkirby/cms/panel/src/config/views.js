export default {
  site: {
    link: "/site",
    icon: "page",
    menu: true
  },
  users: {
    link: "/users",
    icon: "users",
    menu: true
  },
  languages: {
    link: "/languages",
    icon: "globe",
    menu: true
  },
  account: {
    link: "/account",
    icon: "users",
    menu: false
  },
  ...window.panel.plugins.views
};
