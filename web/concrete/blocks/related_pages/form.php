<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<fieldset>
    <div class="form-group" data-row="mode-page">
        <label class="control-label" for="attributeKeySelect"><?=t('Topic Attribute to Use For Relation')?></label>
        <select class="form-control" name="topicAttributeKeyHandle" id="attributeKeySelect">
            <? foreach($attributeKeys as $attributeKey) { ?>
                <option value="<?=$attributeKey->getAttributeKeyHandle()?>" <? if ($attributeKey->getAttributeKeyHandle() == $topicAttributeKeyHandle) { ?>selected<? } ?>><?=$attributeKey->getAttributeKeyDisplayName()?></option>
            <? } ?>
        </select>
    </div>

    <div class='form-group'>
        <label for='title' style="margin-bottom: 0px;"><?=t('Parent Page')?>:</label>
        <div class="checkbox">
            <label>
                <input <? if (intval($cParentID) > 0) { ?>checked<? } ?> name="filterByParent" type="checkbox" value="1" />
                <?=t('Filter by Parent Page')?>
            </label>
        </div>
        <div id="ccm-block-related-pages-parent-page">
            <?
            print Loader::helper('form/page_selector')->selectPage('cParentID', $cParentID);
            ?>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label"><?= t('Filter by Page Type') ?></label>
        <?php ?>
            <select class="form-control" name="ptID" id="selectPTID">
                <option value="0">** <?php echo t('All') ?> **</option>
                <?php
                foreach ($pagetypes as $ct) {
                    ?>
                    <option
                        value="<?= $ct->getPageTypeID() ?>" <? if ($ptID == $ct->getPageTypeID()) { ?> selected <? } ?>>
                        <?= $ct->getPageTypeName() ?>
                    </option>
                <?php
                }
                ?>
            </select>
        <?php ?>
    </div>

    <div class="form-group">
        <label class="control-label" for="maxResults"><?=t('Number of Pages to Display')?></label>
        <input class="form-control" name="maxResults" id="maxResults" value="<?=$maxResults?>" />
    </div>

    <div class="form-group">
        <label class="control-label" for="title"><?=t('Title')?></label>
        <input class="form-control" name="title" id="title" value="<?=$title?>" />
    </div>

</fieldset>


<script type="text/javascript">
    $(function() {
        $("input[name=filterByParent]").on('change', function() {
            if ($(this).is(":checked")) {
                $('#ccm-block-related-pages-parent-page').show();
            } else {
                $('#ccm-block-related-pages-parent-page').hide();
            }
        }).trigger('change');
    });
</script>
