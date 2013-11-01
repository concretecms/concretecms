<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-ui">
<div class="row">
<div class="span10 offset1 columns">
<div class="ccm-pane">
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeader(t('Block Types'), t('Add custom block types, refresh the database tables of installed blocks, and uninstall blocks types from here.'));?>
<? if ($this->controller->getTask() == 'inspect' || $this->controller->getTask() == 'refresh') { ?>

<div class="ccm-pane-body">
	
	<h3><img src="<?=$ci->getBlockTypeIconURL($bt)?>" /> <?=t($bt->getBlockTypeName())?></h3>
		
	<h5><?=t('Description')?></h5>
	<p><?=t($bt->getBlockTypeDescription())?></p>

	<h5><?=t('Usage Count')?></h5>
	<p><?=$num?></p>
	
	<h5><?php echo t('Usage Count on Active Pages')?></h5>
	<p><?php echo $numActive?></p>
	
	<? if ($bt->isBlockTypeInternal()) { ?>
	<h5><?=t('Internal')?></h5>
	<p><?=t('This is an internal block type.')?></p>
	<? } ?>

</div>
<div class="ccm-pane-footer">
	<a href="<?=$this->url('/dashboard/blocks/types')?>" class="btn"><?=t('Back to Block Types')?></a>

	<? print $ch->button(t("Refresh"), $this->url('/dashboard/blocks/types','refresh', $bt->getBlockTypeID()), "right"); ?>
	<?
	$u = new User();
	if ($u->isSuperUser()) {
	
		$removeBTConfirm = t('This will remove all instances of the %s block type. This cannot be undone. Are you sure?', $bt->getBlockTypeHandle());
		
		print $ch->button_js(t('Remove'), 'removeBlockType()', 'right', 'error');?>

		<script type="text/javascript">
		removeBlockType = function() {
			if (confirm('<?=$removeBTConfirm?>')) { 
				location.href = "<?=$this->url('/dashboard/blocks/types', 'uninstall', $bt->getBlockTypeID(), $valt->generate('uninstall'))?>";				
			}
		}
		</script>

	<? } else { ?>
		<? print $ch->button_js(t('Remove'), 'alert(\'' . t('Only the super user may remove block types.') . '\')', 'right', 'disabled error');?>
	<? } ?>
		
</div>

<? } else { ?>

<div class="ccm-pane-body ccm-pane-body-footer">

	<h5><?=t('Awaiting Installation')?></h5>
	<? if (count($availableBlockTypes) > 0) { ?>
		<ul id="ccm-block-type-list">
		<?	foreach ($availableBlockTypes as $bt) { 
			$btIcon = $ci->getBlockTypeIconURL($bt);
			?>
			<li class="ccm-block-type ccm-block-type-available">
				<p style="background-image: url(<?=$btIcon?>)" class="ccm-block-type-inner"><?=$ch->button(t("Install"), $this->url('/dashboard/blocks/types','install', $bt->getBlockTypeHandle()), "right", 'small');?> <?=t($bt->getBlockTypeName())?></p>
			</li>
		<? } ?>
		</ul>
	<? } else { ?>
		<p><?=t('No custom block types are awaiting installation.')?></p>
	<? } ?>
	
    <? if (ENABLE_MARKETPLACE_SUPPORT == true) { ?>
	<div class="well" style="padding:10px 20px;">
        <h3><?=t('More Blocks')?></h3>
        <p><?=t('Browse our marketplace of add-ons to extend your site!')?></p>
        <p><a class="btn success" href="<?=$this->url('/dashboard/extend/add-ons')?>"><?=t("More Add-ons")?></a></p>
    </div>
    <? } ?>
    
	<h3><?=t('Installed Block Types')?></h3>
	<div id="ccm-block-type-list-installed" class="ccm-block-type-sortable-list">
		<? foreach($normalBlockTypes as $bt) { 
			$btIcon = $ci->getBlockTypeIconURL($bt);
			$btID = $bt->getBlockTypeID();
			?>
			<div class="ccm-group" id="btID_<?=$btID?>" data-btid="<?=$btID?>">
				<img class="ccm-group-sort" src="<?php echo ASSETS_URL_IMAGES?>/icons/up_down.png" width="14" height="14" />
				<a class="ccm-group-inner" href="<?=$this->action('inspect', $bt->getBlockTypeID())?>" style="background-image: url(<?=$btIcon?>)"><?=t($bt->getBlockTypeName())?></a>
			</div>
		<? } ?>
	</div>
	<script type="text/javascript">
	$(document).ready(function() {
		$("#ccm-block-type-list-installed").sortable({
			handle: 'img.ccm-group-sort',
			cursor: 'move',
			opacity: 0.5,
			stop: function(event, ui) {
				var btID = ui.item.attr('data-btid');
				var btDisplayOrder = ui.item.index() + 1;
				var data = 'btID=' + btID + '&btDisplayOrder=' + btDisplayOrder;
				$.post('<?=(REL_DIR_FILES_TOOLS_REQUIRED . "/dashboard/block_type_order_update")?>', data);
			}
		});
	});
	</script>
	<div style="padding: 10px 0 20px 0;">
		<form action="<?=$this->action('reset_display_order')?>" method="post">
			<?
			$prompt = t('Are you sure you wish to reset the display order of installed block types?');
			$onclick = "if (confirm('" . $prompt . "')) { $(this).closest('form').submit(); }";
			echo Loader::helper('concrete/interface')->button_js(t('Reset Order'), $onclick, 'right', 'small');
			echo Loader::helper('form')->hidden('isSubmitted', '1');
			?>
		</form>
	</div>
	
	<h5><?=t('Internal Block Types')?></h5>
	<ul id="ccm-block-type-list">
		<? foreach($internalBlockTypes as $bt) { 
			$btIcon = $ci->getBlockTypeIconURL($bt);
			?>	
			<li class="ccm-block-type ccm-block-type-available">
				<a style="background-image: url(<?=$btIcon?>)" class="ccm-block-type-inner" href="<?=$this->action('inspect', $bt->getBlockTypeID())?>"><?=t($bt->getBlockTypeName())?></a>
				<div class="ccm-block-type-description"  id="ccm-bt-help<?=$bt->getBlockTypeID()?>"><?=t($bt->getBlockTypeDescription())?></div>
			</li>
		<? } ?>
	</ul>
	
	

</div>
	
<? } ?>
</div>
</div>
</div>
</div>
