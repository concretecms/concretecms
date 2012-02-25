<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-pane-controls">
<?
$sh = Loader::helper('concrete/dashboard/sitemap');
$numChildren = $c->getNumChildren();
?>

<style type="text/css">
div#ccm-mc-page h1#ccm-sitemap-title {display: none}
</style>

<h1><?=t('Move, Copy or Delete this Page')?></h1>

<div class="ccm-form-area" id="ccm-mc-page">	
	<h2><?=t('Move/Copy Page')?></h2>

	<? 
	if ($sh->canRead()) { ?>
	
	<p><?=t("Click below to move or copy the current page to a particular spot in your site.")?></p>

	<?
		$select_mode = 'move_copy_delete';
		include(DIR_FILES_TOOLS_REQUIRED . '/sitemap_search_selector.php');
	
	} else {
		?>
		<p><?
		print t('You do not have access to the sitemap. You must have access to move or copy this page.');
		?></p>
		
		
		<?
	
	}
	
	?>

	<div class="ccm-spacer">&nbsp;</div>
</div>
<?

if ($cp->canDeletePage()) { ?>

<div class="ccm-form-area" style="margin-top: 10px">
			<? if ($c->getCollectionID() == 1) {  ?>
				<h2><?=t('Delete Page')?></h2>
				<?=t('You may not delete the home page.');?>
			<? } else {	?>
				<? if ($c->isPendingDelete()) { ?>
					<h2><?=t('Delete Page')?></h2>
					<span class="important"><?=t('This page has been marked for deletion.')?></span>
					<?
					
					$u = new User();
					$puID = $u->getUserID();
					
					if ($puID == $c->getPendingActionUserID()) { ?>
						<br><br>
						<?=t('You marked this page for deletion on <strong>%s</strong>', date(DATE_APP_PAGE_VERSIONS, strtotime($c->getPendingActionDateTime())))?><br><br>
						<form method="get" id="ccmDeletePageForm" action="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>">
							<a href="javascript:void(0)" onclick="$('#ccmDeletePageForm').get(0).submit()" class="ccm-button-left"><span><?=t('Cancel')?></span></a>
							<input type="hidden" name="cID" value="<?=$c->getCollectionID()?>">
							<input type="hidden" name="ctask" value="clear_pending_action">
						</form>
					<? } ?>
				<? } else if ($c->isPendingMove() || $c->isPendingCopy()) { ?>
					<h2><?=t('Delete Page')?></h2>
					<?=t('Since this page is being moved or copied, it cannot be deleted.')?>
				<? } else if ($numChildren > 0 && !$cp->canAdminPage()) { ?>
					<h2><?=t('Delete Page')?></h2>
					<?=t('Before you can delete this page, you must delete all of its child pages.')?>
				<? } else { 
					$deletePageMsg = t('Are you sure you wish to delete this page?');
					?>
					
					<div class="ccm-buttons">

					<form method="post" id="ccmDeletePageForm" action="<?=$c->getCollectionAction()?>">	
						<a href="javascript:void(0)" onclick="if (confirm('<?=$deletePageMsg?>')) { $('#ccmDeletePageForm').get(0).submit()}" class="ccm-button-right accept"><span><?=t('Delete Page')?></span></a>
					<h2><?=t('Delete Page')?></h2>
					<? if ($cp->canAdminPage() && $numChildren > 0) { ?>
						<span class="important"><?=t('This will remove %s child page(s).', $numChildren)?></span>
					<? } ?>
						<input type="hidden" name="cID" value="<?=$c->getCollectionID()?>">
						<input type="hidden" name="ctask" value="delete">
					</form>
					</div>
					
				<? }
			}?>
<div class="ccm-spacer">&nbsp;</div>
</div>

<? } ?>

</div>