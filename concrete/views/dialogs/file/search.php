<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div data-choose="file-manager" class="h-100">
    <concrete-file-chooser></concrete-file-chooser>
</div>
<script type="text/javascript">

    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'div[data-choose=file-manager]',
            components: config.components
        })
    })

</script>