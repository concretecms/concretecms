<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php
$options = [];
$selectedIDs = [];
if (isset($selectedEntities)) {
    foreach ($selectedEntities as $selectedEntity) {
        $options[] = [
            'exEntryID' => $selectedEntity->getID(),
            'label' => $selectedEntity->getLabel(),
        ];
        $selectedIDs[] = $selectedEntity->getID();
    }
}
?>
<div class="mb-3">
    <?php if ($view->supportsLabel()) {
    ?>
        <label class="form-label" for="<?=$view->getControlID(); ?>"><?=$label; ?></label>
    <?php
} ?>
    <?php if ($view->isRequired()) { ?>
        <span class="text-muted small"><?=t('Required')?></span>
    <?php } ?>

    <select multiple data-select-and-add="<?= $control->getId(); ?>" class="form-control form-select" name="express_association_<?= $control->getId(); ?>[]">
        <?php foreach ($options as $option) { ?>
            <option selected value="<?=$option['exEntryID']?>"><?=$option['label']?></option>
        <?php } ?>
    </select>
</div>

<script type="text/javascript">
    $(function() {
        $('select[data-select-and-add="<?= $control->getId(); ?>"]').selectpicker(
            {
                liveSearch: true
            }
        ).ajaxSelectPicker(
            {
                ajax: {
                    url: "<?= \URL::to('/ccm/system/express/entry/get_json'); ?>",
                    data: {
                        'exEntityID': '<?= $control->getAssociation()->getTargetEntity()->getID(); ?>',
                        'keyword': "{{{q}}}"
                    },
                    method: 'get'
                },
                preprocessData: function(data) {
                    var entries = []
                    if (data.hasOwnProperty('data')) {
                        data.data.forEach(function(entry) {
                            entries.push({
                                'value': entry.exEntryID,
                                'text': entry.label
                            })
                        })
                    }
                    return entries
                },
                locale: {
                    currentlySelected: "<?=t('Currently Selected'); ?>",
                    emptyTitle: "<?=t('Select and begin typing'); ?>",
                    errorText: "<?=t('Unable to retrieve results'); ?>",
                    searchPlaceholder: "<?=t('Search...'); ?>",
                    statusInitialized: "<?=t('Start typing a search query'); ?>",
                    statusNoResults: "<?=t('No Results'); ?>",
                    statusSearching: "<?=t('Searching...'); ?>",
                    statusTooShort: "<?=t('Please enter more characters'); ?>",
                },
                preserveSelected: false,
                clearOnEmpty: false,
                minLength: 2,
            },
        );
    });


</script>