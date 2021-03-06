import Vue from "vue";
import Api from "@/api/api.js";

export default {
  namespaced: true,
  state: {
    all: [],
    current: null,
    default: null
  },
  mutations: {
    SET_ALL(state, languages) {
      state.all = languages.map(language => {
        return {
          code: language.code,
          name: language.name,
          default: language.default,
          direction: language.direction
        };
      });
    },
    SET_CURRENT(state, language) {
      state.current = language;
    },
    SET_DEFAULT(state, language) {
      state.default = language;
    }
  },
  actions: {
    current(context, language) {
      context.commit("SET_CURRENT", language);
    },
    install(context, languages) {
      const defaultLanguage = languages.filter(language => language.default)[0];

      context.commit("SET_ALL", languages);
      context.commit("SET_DEFAULT", defaultLanguage);
      context.commit("SET_CURRENT", defaultLanguage || languages[0]);
    },
    load(context) {
      return Api.get("languages").then(response => {
        context.dispatch("install", response.data);
      });
    }
  }
};
