<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php if ($this->controller->getTask() == 'inspect' || $this->controller->getTask() == 'refresh') { ?>


<h3><img src="<?=$ci->getBlockTypeIconURL($bt)?>" /> <?=t($bt->getBlockTypeName())?></h3>

<h5><?=t('Description')?></h5>
<p><?=t($bt->getBlockTypeDescription())?></p>

<h5><?=t('Usage Count')?></h5>
<p><?=$num?></p>

<h5><?php echo t('Usage Count on Active Pages')?></h5>
<p><?php echo $numActive?></p>

<?php if ($bt->isBlockTypeInternal()) { ?>
<h5><?=t('Internal')?></h5>
<p><?=t('This is an internal block type.')?></p>
<?php } ?>

<hr/>

<a href="<?=$view->url('/dashboard/blocks/types')?>" class="btn btn-default pull-left"><?=t('Back to Block Types')?></a>
    <?
    $u = new User();
    if ($u->isSuperUser()) { ?>

    <div class="btn-group pull-right">
       <a href="<?=URL::to('/dashboard/blocks/types', 'refresh', $bt->getBlockTypeID())?>" class="btn btn-default"><?=t('Refresh')?></a>
       <a href="javascript:void(0)" class="btn btn-danger" onclick="removeBlockType()"><?=t('Remove')?></a>
    </div>

    <script type="text/javascript">
        removeBlockType = function() {
            if (confirm('<?=t('This will remove all instances of the %s block type. This cannot be undone. Are you sure?', $bt->getBlockTypeHandle())?>')) {
                location.href = "<?=$view->url('/dashboard/blocks/types', 'uninstall', $bt->getBlockTypeID(), $token->generate('uninstall'))?>";
            }
        }
    </script>

    <?php } else { ?>
        <a href="<?=URL::to('/dashboard/blocks/types', 'refresh', $bt->getBlockTypeID())?>" class="btn btn-default"><?=t('Refresh')?></a>
    <?php } ?>
</div>

<?php } else { ?>

	<h3><?=t('Awaiting Installation')?></h3>
	<?php if (count($availableBlockTypes) > 0) { ?>

        <ul class="item-select-list">
            <?	foreach ($availableBlockTypes as $bt) {
                $btIcon = $ci->getBlockTypeIconURL($bt);
                ?>
                <li><span><img src="<?=$btIcon?>" /> <?=t($bt->getBlockTypeName())?>
                    <a href="<?=URL::to('/dashboard/blocks/types','install', $bt->getBlockTypeHandle())?>" class="btn pull-right btn-sm btn-default"><?=t('Install')?></a>
                    </span>
                </li>
            <?php } ?>
        </ul>

	<?php } else { ?>
		<p><?=t('No custom block types are awaiting installation.')?></p>
	<?php } ?>

    <?php if (Config::get('concrete.marketplace.enabled') == true) { ?>
    <div class="alert alert-info">
        <a class="btn btn-success btn-xs pull-right" href="<?=$view->url('/dashboard/extend/add-ons')?>"><?=t("More Add-ons")?></a>
        <p><?=t('Browse our marketplace of add-ons to extend your site!')?></p>
    </div>
    <?php } ?>

    <hr/>

	<h3><?=t('Installed Block Types')?></h3>
	<ul id="ccm-block-type-list-installed" class="item-select-list ccm-block-type-sortable-list">
		<?php foreach($normalBlockTypes as $bt) {
			$btIcon = $ci->getBlockTypeIconURL($bt);
			$btID = $bt->getBlockTypeID();
			?>
			<li id="btID_<?=$btID?>" data-btid="<?=$btID?>">
                <a href="<?=$view->action('inspect', $bt->getBlockTypeID())?>"><img src="<?=$btIcon?>" /> <?=t($bt->getBlockTypeName())?></a>
			</li>
		<?php } ?>
	</ul>

	<h3><?=t('Internal Block Types')?></h3>
    <ul class="item-select-list">
		<?php foreach($internalBlockTypes as $bt) {
			$btIcon = $ci->getBlockTypeIconURL($bt);
			?>
			<li>
                <a href="<?=$view->action('inspect', $bt->getBlockTypeID())?>"><img src="<?=$btIcon?>" /> <?=t($bt->getBlockTypeName())?></a>
			</li>
		<?php } ?>
	</ul>



</div>

<?php } ?>
