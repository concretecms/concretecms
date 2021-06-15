<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div data-choose="group-search" class="h-100">
    <concrete-group-chooser mode="select"></concrete-group-chooser>
</div>
<script type="text/javascript">

    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'div[data-choose=group-search]',
            components: config.components
        })
    })

</script>