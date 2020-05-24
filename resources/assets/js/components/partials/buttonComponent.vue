<template>
  <button  
  @click="btnClick" 
  :disabled="btnDisabled" 
  class="baseButton py-3 px-4 rounded mb-8 no-underline text-base shadow hover:shadow-md active:shadow-xs transition-all duration-100 ease-linear"
  :class="{
      'text-white font-medium': btnType,
      'text-indigo-500 bg-white': !btnType,
      'bg-indigo-600' : btnType === 'primary',
      'bg-red-600' : btnType === 'danger',
    }"
  >
    <slot></slot>
  </button>
</template>

<script>
export default {
  props: {
    btnDisabled: {
      type: Boolean,
      required: false,
      default: false,
    },
    btnType: {
      type: String,
      required: false,
      default: null
    },
    btnSize: {
      type: String,
      required: false,
      default: null
    }
  },
  methods: {
    btnClick() {
      this.$emit('click');
    }
  }
}
</script>

<style>
.baseButton {
  transform: scale(1);
  outline: none;
}
.baseButton:hover {
  transform: scale(1.001);
}
.baseButton:disabled {
  opacity: 0.5;
}
.baseButton:active {
  transform: scale(0.98);
  outline: none;
  box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.05);
}
.baseButton:focus {
  outline: none;
}
</style>