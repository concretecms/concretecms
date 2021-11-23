<?php
defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var bool $multipleSelection
 * @var \Concrete\Core\File\Component\Chooser\ChooserConfiguration $configuration
 */

$uploaders = $configuration->getUploaders();
$choosers = $configuration->getChoosers();
?>

<div data-choose="file-manager" class="h-100">
    <concrete-file-chooser
            :uploaders='<?= json_encode($uploaders)?>'
            :choosers='<?= json_encode($choosers)?>'
            :multiple-selection="<?= json_encode($multipleSelection); ?>">

    </concrete-file-chooser>
</div>
<script type="text/javascript">

    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'div[data-choose=file-manager]',
            components: config.components
        })
    })

</script>