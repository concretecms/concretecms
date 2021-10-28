<?php
defined('C5_EXECUTE') or die("Access Denied.");
$rules = [];
foreach ($boardInstance->getRules() as $rule) {
    $rules[] = $rule;
}
// The above is necessary because getRules() is not necessarily eager loaded, so if we just json_decode it it'll be empty.
?>

<div class="ccm-ui">
    <div data-view="board-instance-scheduler">

        <div class="d-flex align-items-center mb-3">
            <div><?=t('Below is a list of custom content rules for this board.')?></div>
            <a href="#" data-button-action="add-scheduled-slot" class="ms-auto btn btn-secondary"><?=t('Add Scheduled Slot')?></a>
        </div>

        <?php if (count($rules)) { ?>
            <board-instance-rule-list :rules="rules"></board-instance-rule-list>
        <?php } else { ?>
            <p><?=t('There are no scheduled content slots for this board instance.')?></p>
        <?php } ?>

    </div>
</div>

<script type="text/javascript">

    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'div[data-view=board-instance-scheduler]',
            components: config.components,
            mounted() {
                $('a[data-button-action=add-scheduled-slot]').on('click', function(e) {
                    e.preventDefault()
                    $.fn.dialog.open({
                        href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/boards/custom_slot/replace?slot=1&boardInstanceID=<?=h($boardInstance->getBoardInstanceID())?>',
                        width: '960',
                        height: '720',
                        title: <?=json_encode(t('Add Scheduled Slot'))?>,
                    })
                })
            },
            methods: {

            },
            computed: {

            },
            watch: {},
            data: {
                rules: <?=json_encode($rules)?>
            }
        })
    })


</script>
