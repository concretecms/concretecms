<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<ul class="ccm-inline-toolbar ccm-ui">
    <li><label><a target="_blank" href="<?php echo URL::to('/dashboard/boards/instances/details', $instance->getBoardInstanceID())?>">
        <?php echo $instance->getBoardInstanceName()?>
    </a></li>
    <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save">
        <button type="button" data-toolbar-button-action="exit-board"><?= t("Done") ?></button>
    </li>
</ul>

<?php
$renderer->render($instance);
?>

<script type="text/javascript">
    $(function () {
        $('button[data-toolbar-button-action=exit-board]').on('click', function () {
            ConcreteEvent.fire('EditModeExitInline');
        });

        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: 'div[data-vue=board]',
                components: config.components
            })
        })

    });
</script>