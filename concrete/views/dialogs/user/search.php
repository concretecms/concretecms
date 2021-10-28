<?php
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var bool $multipleSelection
 */
?>

<div data-choose="user" class="h-100">
    <concrete-user-chooser :multiple-selection="<?= json_encode($multipleSelection); ?>"></concrete-user-chooser>
</div>
<script type="text/javascript">

    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'div[data-choose=user]',
            components: config.components
        })
    })

</script>
