<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div class="ccm-pane-controls">
<?php 
$children = $c->getCollectionChildrenArray();
$numChildren = count($children);
$sh = Loader::helper('concrete/dashboard/sitemap');

?>
<script type="text/javascript">
	var childPages = new Array();
	<?php  foreach($children as $cID) { ?>
		childPages.push(<?php echo $cID?>);
	<?php  } ?>
</script>

<style type="text/css">
div#ccm-mc-page h1#ccm-sitemap-title {display: none}
</style>

<?php  if ($sh->canRead()) { ?>

<h1><?php echo t('Move, Copy or Delete this Page')?></h1>

<div class="ccm-form-area" id="ccm-mc-page">	
	<h2><?php echo t('Move/Copy Page')?></h2>
	<p><?php echo t("Click below to move or copy the current page to a particular spot in your site.")?></p>
	

	<?php  
	
	$args = array();
	//$args['reveal'] = $c->getCollectionID();
	$args['sitemap_mode'] = 'move_copy_delete';
	$args['sitemap_disable_auto_open'] = true;
	Loader::element('dashboard/sitemap', $args);
	
	?>
	
	<script type="text/javascript">$(function() {
		$('#ccm-launch-sitemap').dialog();
	});
	</script>

	<div class="ccm-spacer">&nbsp;</div>
</div>
<?php  }

if ($cp->canDeleteCollection()) { ?>

<div class="ccm-form-area" style="margin-top: 10px">
			<?php  if ($c->getCollectionID() == 1) {  ?>
				<h2><?php echo t('Delete Page')?></h2>
				<?php echo t('You may not delete the home page.');?>
			<?php  } else {	?>
				<?php  if ($c->isPendingDelete()) { ?>
					<h2><?php echo t('Delete Page')?></h2>
					<span class="important"><?php echo t('This page has been marked for deletion.')?></span>
					<?php 
					
					$u = new User();
					$puID = $u->getUserID();
					
					if ($puID == $c->getPendingActionUserID()) { ?>
						<br><br>
						<?php echo t('You marked this page for deletion on <strong>%s</strong>', $c->getPendingActionDateTime())?><br><br>
						<form method="get" id="ccmDeletePageForm" action="<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>">
							<a href="javascript:void(0)" onclick="$('#ccmDeletePageForm').get(0).submit()" class="ccm-button-left"><span><?php echo t('Cancel')?></span></a>
							<input type="hidden" name="cID" value="<?php echo $c->getCollectionID()?>">
							<input type="hidden" name="ctask" value="clear_pending_action">
						</form>
					<?php  } ?>
				<?php  } else if ($c->isPendingMove() || $c->isPendingCopy()) { ?>
					<h2><?php echo t('Delete Page')?></h2>
					<?php echo t('Since this page is being moved or copied, it cannot be deleted.')?>
				<?php  } else if ($numChildren > 0 && !$cp->canAdminPage()) { ?>
					<h2><?php echo t('Delete Page')?></h2>
					<?php echo t('Before you can delete this page, you must delete all of its child pages.')?>
				<?php  } else { 
					$deletePageMsg = t('Are you sure you wish to delete this page?');
					?>
					
					<div class="ccm-buttons">

					<form method="post" id="ccmDeletePageForm" action="<?php echo $c->getCollectionAction()?>">	
						<a href="javascript:void(0)" onclick="if (confirm('<?php echo $deletePageMsg?>')) { $('#ccmDeletePageForm').get(0).submit()}" class="ccm-button-right accept"><span><?php echo t('Delete Page')?></span></a>
					<h2><?php echo t('Delete Page')?></h2>
					<?php  if ($cp->canAdminPage() && $numChildren > 0) { ?>
						<span class="important"><?php echo t('This will remove %s child page(s).', $numChildren)?></span>
					<?php  } ?>
						<input type="hidden" name="cID" value="<?php echo $c->getCollectionID()?>">
						<input type="hidden" name="ctask" value="delete">
					</form>
					</div>
					
				<?php  }
			}?>
<div class="ccm-spacer">&nbsp;</div>
</div>

<?php  } ?>

</div>