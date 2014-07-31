<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<fieldset>
    <div class="form-group">
        <label class="control-label" for="topicTreeIDSelect"><?=t('Topic Tree')?></label>
        <select class="form-control" name="topicTreeID" id="topicTreeIDSelect">
            <? foreach($trees as $stree) { ?>
                <option value="<?=$stree->getTreeID()?>" <? if ($tree->getTreeID() == $stree->getTreeID()) { ?>selected<? } ?>><?=$stree->getTreeDisplayName()?></option>
            <? } ?>
        </select>
    </div>

    <div class='form-group'>
        <label for='title' style="margin-bottom: 0px;"><?=t('Results Page')?>:</label>
        <div class="checkbox">
            <label for="ccm-search-block-external-target">
                <input id="ccm-search-block-external-target" <? if (intval($cParentID) > 0) { ?>checked<? } ?> name="externalTarget" type="checkbox" value="1" />
                <?=t('Post Results to a Different Page')?>
            </label>
        </div>
        <div id="ccm-search-block-external-target-page">
        <?
        print Loader::helper('form/page_selector')->selectPage('cParentID', $cParentID);
        ?>
        </div>
    </div>

</fieldset>

<script type="text/javascript">
$(function() {
   $("input[name=externalTarget]").on('change', function() {
       if ($(this).is(":checked")) {
           $('#ccm-search-block-external-target-page').show();
       } else {
           $('#ccm-search-block-external-target-page').hide();
       }
   }).trigger('change');
});
</script>