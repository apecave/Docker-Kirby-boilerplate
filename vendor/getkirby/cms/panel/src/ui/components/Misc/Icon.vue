<template>
  <span
    :aria-label="alt"
    :role="alt ? 'img' : null"
    :aria-hidden="!alt"
    :data-back="back"
    :data-size="size"
    class="k-icon"
  >
    <span v-if="emoji" class="k-icon-emoji">{{ type }}</span>
    <svg v-else viewBox="0 0 16 16">
      <use :xlink:href="'#icon-' + type" />
    </svg>
  </span>
</template>

<script>
export default {
  props: {
    alt: String,
    back: String,
    emoji: Boolean,
    size: String,
    type: String,
  }
};
</script>

<style lang="scss">
.k-icon {
  position: relative;
  line-height: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.k-icon svg {
  width: 1rem;
  height: 1rem;
}
.k-icon svg * {
  fill: currentColor;
}
.k-icon[data-back="black"] {
  background: $color-dark;
  color: $color-white;
}
.k-icon[data-back="white"] {
  background: $color-white;
}
.k-icon[data-back="pattern"] {
  background: lighten($color-dark, 10%) url($pattern);
  color: $color-white;
}
.k-icon[data-size="medium"] svg {
  width: 2rem;
  height: 2rem;
}
.k-icon[data-size="large"] svg {
  width: 3rem;
  height: 3rem;
}
.k-icon-emoji {
  display: block;
  line-height: 1;
  font-style: normal;
  font-size: 1rem;
}
</style>
