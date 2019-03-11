import Vue from 'vue';

// Load up our cropper
Vue.component('avatar-cropper', require('./avatar/Cropper.vue'));

const app = new Vue({
    el: '[vue-enabled]',
});
