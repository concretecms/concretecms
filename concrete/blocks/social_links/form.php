<?php defined('C5_EXECUTE') or die("Access Denied.");  ?>

<div class="form-group">
    <label class="control-label"><?=t('Choose Social Links to Show')?></label>
    <div id="ccm-block-social-links-list">
    <?
    if (count($links) == 0) { ?>
        <p><?=t('You have not added any social links.')?></p>
    <? }

    foreach($links as $link) {
        $service = $link->getServiceObject();
        ?>
        <div class="checkbox">
            <label><input type="checkbox" name="slID[]"
               <? if (is_array($selectedLinks) && in_array($link, $selectedLinks)) { ?>
                 checked
                <? } ?>
            value="<?=$link->getID()?>" />
            <?=$service->getDisplayName()?>
            </label>

            <i class="pull-right fa fa-arrows"></i>
        </div>
    <? } ?>
    </div>
</div>

<hr/>
<div class="alert alert-info">
    <?=t('Add social links <a href="%s">in the dashboard</a>', URL::to('/dashboard/system/basics/social'))?>
</div>

<style type="text/css">
#ccm-block-social-links-list {
    -webkit-user-select: none;
}
#ccm-block-social-links-list i.fa-arrows {
    display: none;
    color: #666;
    cursor: move;
}

#ccm-block-social-links-list div.checkbox:hover i.fa-arrows {
    display: block;
}
</style>

<script type="text/javascript">
$(function() {
    $('#ccm-block-social-links-list').sortable();
});
</script>