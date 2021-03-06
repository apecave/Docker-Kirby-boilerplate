<template>
  <div class="k-datetime-input">
    <k-date-input
      ref="dateInput"
      :autofocus="autofocus"
      :required="required"
      :id="id"
      :disabled="disabled"
      :value="dateValue"
      @input="setDate"
    />
    <k-time-input
      ref="timeInput"
      :required="required"
      :disabled="disabled"
      :value="timeValue"
      v-bind="timeOptions"
      @input="setTime"
    />
  </div>
</template>

<script>
import DateInput from "./DateInput.vue";
import dayjs from "dayjs";
import { required } from "vuelidate/lib/validators";

export default {
  inheritAttrs: false,
  props: {
    ...DateInput.props,
    time: {
      type: [Boolean, Object],
      default() {
        return {};
      }
    },
    value: String
  },
  data() {
    return {
      dateValue: this.parseDate(this.value),
      timeValue: this.parseTime(this.value),
      timeOptions: this.setTimeOptions(),
    };
  },
  watch: {
    value(value) {
      this.dateValue = this.parseDate(value);
      this.timeValue = this.parseTime(value);
      this.onInvalid();
    }
  },
  mounted() {
    this.onInvalid();
  },
  methods: {
    focus() {
      this.$refs.input.focus();
    },
    onInput() {
      if (this.dateValue === null) {
        this.$emit("input", null);
        return;
      }

      let value = this.timeValue === null ?
          this.dateValue :
          this.dateValue + "T" + this.timeValue;

      this.$emit("input", value);
    },
    onInvalid() {
      this.$emit("invalid", this.$v.$invalid, this.$v);
    },
    parseDate(value) {
      const dt = dayjs(value);
      return dt.isValid() ? dt.format("YYYY-MM-DD") : null;
    },
    parseTime(value) {
      const dt = dayjs(value);
      return dt.isValid() ? dt.format("HH:mm") : null;
    },
    setDate(value) {
      this.dateValue = this.parseDate(value);
      this.onInput();
    },
    setTime(value) {
      this.timeValue = value;
      this.onInput();
    },
    setTimeOptions() {
      return this.time === true ? {} : this.time;
    }
  },
  validations() {
    return {
      value: {
        required: this.required ? required : true
      }
    };
  }
}

</script>

<style lang="scss">
.k-datetime-input {
  display: flex;
}
.k-datetime-input .k-time-input {
  padding-left: $field-input-padding;
}
</style>
