<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<div style="width: 760px">

<?php 
$tp1 = new TaskPermission();
if ($tp1->canAccessTaskPermissions()) { 
	$ih = Loader::helper('concrete/interface');
	$tp = TaskPermission::getByHandle('access_sitemap');
	?>
	
	<h1><span><?php echo t('Sitemap Permissions')?></span></h1>
	<div class="ccm-dashboard-inner">
		<form method="post" id="sitemap-permissions" action="<?php echo $this->url('/dashboard/sitemap/access', 'save_permissions')?>">
			<?php echo $validation_token->output('sitemap_permissions');?>
	
			<?php  print $h->getForm($tp, t('Add users or groups to determine access to the file manager. <strong>Note:</strong> If you want users to have access to the dashboard sitemap, they must be entered here and in the dashboard sitemap page permissions area.')); ?>
			
			<div class="ccm-spacer">&nbsp;</div>
			
			
			<?php  print $ih->submit(t('Save'), 'sitemap-permissions'); ?>
	
			<div class="ccm-spacer">&nbsp;</div>
		</form>
	</div>
<?php  } else { ?>
	<h1><span><?php echo t('Sitemap Permissions')?></span></h1>
	<div class="ccm-dashboard-inner">
	<?php echo t('You are not allowed to change who can access the sitemap.')?>
	</div>
<?php  } ?>

	</div>
