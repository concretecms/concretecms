<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<fieldset>
    <div clxmlass="form-group">
        <label class="control-label" for="topicTreeIDSelect"><?=t('Topic Tree')?></label>
        <select class="form-control" name="topicTreeID" id="topicTreeIDSelect">
            <? foreach($trees as $stree) { ?>
                <option value="<?=$stree->getTreeID()?>" <? if ($tree->getTreeID() == $stree->getTreeID()) { ?>selected<? } ?>><?=$stree->getTreeDisplayName()?></option>
            <? } ?>
        </select>
    </div>
</fieldset>
