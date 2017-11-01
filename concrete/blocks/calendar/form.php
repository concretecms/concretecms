<?php
defined('C5_EXECUTE') or die("Access Denied.");
$color = \Core::make('helper/form/color');
?>

<fieldset>
    <legend><?=t('Data Source')?></legend>

    <?php View::element('calendar/block/data_source', ['caID' => $caID, 'calendarAttributeKeyHandle' => $calendarAttributeKeyHandle], 'calendar') ?>

</fieldset>

<fieldset>
    <legend><?=t('Filtering')?></legend>

    <div class="form-group">
        <label class="control-label" for="totalToRetrieve"><?= t('Filter by Topic Attribute') ?></label>
        <select class="form-control" name="filterByTopicAttributeKeyID">
            <option value=""><?=t('** None')?></option>
            <?php foreach ($attributeKeys as $ak) {
    $attributeController = $ak->getController();
    ?>
                <option value="<?=$ak->getAttributeKeyID()?>" <?php if ($ak->getAttributeKeyID() == $filterByTopicAttributeKeyID) {
    ?>selected<?php 
}
    ?> data-tree-id="<?=$attributeController->getTopicTreeID()?>"><?=$ak->getAttributeKeyDisplayName()?></option>
            <?php 
} ?>
        </select>
        <input type="hidden" name="filterByTopicID" value="<?=$filterByTopicID?>">
        <div class="tree-view-container">
            <div class="tree-view-template">
            </div>
        </div>
    </div>

</fieldset>

<fieldset>
    <legend><?=t('Lightbox')?></legend>
    <div class="alert alert-info"><?=t('Check any properties that you wish to display in a lightbox. Check none to disable the lightbox.')?></div>

    <?php foreach ($lightboxProperties as $key => $name) {
    ?>
        <div class="checkbox"><label>
                <?=$form->checkbox('lightboxProperties[]', $key, in_array($key, $lightboxPropertiesSelected))?>
                <?=$name?>
            </label>
        </div>
    <?php 
} ?>
</fieldset>

<script type="text/javascript">
    $(function() {

        var treeViewTemplate = $('.tree-view-template');

        $('select[name=filterByTopicAttributeKeyID]').on('change', function() {
            var chosenTree = $(this).find('option:selected').attr('data-tree-id');
            $('.tree-view-template').remove();
            if (!chosenTree) {
                return;
            }
            $('.tree-view-container').append(treeViewTemplate);

            <?php if (compat_is_version_8()) {
    ?>
                $('.tree-view-template').concreteTree({
                    'treeID': chosenTree,
                    'chooseNodeInForm': true,
                    'selectNodesByKey': [<?=intval($filterByTopicID)?>],
                    'onSelect' : function(nodes) {
                        if (nodes.length) {
                            $('input[name=filterByTopicID]').val(nodes[0]);
                        } else {
                            $('input[name=filterByTopicID]').val('');
                        }
                    }
                });
            }).trigger('change');
        <?php 
} else {
    ?>
        $('.tree-view-template').ccmtopicstree({
            'treeID': chosenTree,
            'chooseNodeInForm': true,
            'selectNodesByKey': [<?=intval($filterByTopicID)?>],
            'onSelect' : function(select, node) {
                if (select) {
                    $('input[name=filterByTopicID]').val(node.data.key);
                } else {
                    $('input[name=filterByTopicID]').val('');
                }
            }
        });
    }).trigger('change');
        <?php 
} ?>

    });
</script>