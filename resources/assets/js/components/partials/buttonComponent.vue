<template>
  <button  
  @click="btnClick" 
  :disabled="btnDisabled" 
  class="baseButton py-3 px-4 rounded no-underline text-base shadow hover:shadow-md active:shadow-inner transition-all duration-100 ease-linear"
  :class="{
      'text-white font-medium': btnType,
      'text-indigo-500 bg-white': !btnType,
      'bg-indigo-600' : btnType === 'primary',
      'bg-red-600' : btnType === 'danger',
      'bg-green-600' : btnType === 'success',
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
  outline: none;
  opacity: 0.9;
}
.baseButton:hover{
  opacity: 1;
}
.baseButton:active {
  outline: none;
  box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
}
.baseButton:focus {
  outline: none;
}
.baseButton:disabled {
  opacity: 0.4 !important;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
}
</style>