<?php
use Concrete\Core\File\Upload\Dropzone;

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var bool $multipleSelection
 * @var \Concrete\Core\File\Component\Chooser\ChooserConfiguration $configuration
 */

$uploaders = $configuration->getUploaders();
$choosers = $configuration->getChoosers();
$filters = $configuration->getFilters();
?>

<div data-choose="file-manager" class="h-100">
    <concrete-file-chooser
        :uploaders="<?= h(json_encode($uploaders)) ?>"
        :choosers="<?= h(json_encode($choosers)) ?>"
        <?php
        if ($filters) {
            ?>
            :filters="<?= h(json_encode($filters)) ?>"
            <?php
        }
        ?>
        :multiple-selection="<?= json_encode($multipleSelection) ?>"
        :dropzone-options="<?= h(json_encode(app(Dropzone::class)->getConfigurationOptions())) ?>"
    ></concrete-file-chooser>
</div>
<script type="text/javascript">

    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'div[data-choose=file-manager]',
            components: config.components
        })
    })

</script>