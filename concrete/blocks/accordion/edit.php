<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div data-vue="accordion-block">
    <ckeditor></ckeditor>
</div>

<script>
    Concrete.Vue.activateContext('accordion', function(Vue, config) {
        new Vue({
            el: 'div[data-vue=accordion-block]',
            components: config.components,
        })
    })
</script>

