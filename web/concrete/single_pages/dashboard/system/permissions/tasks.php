<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? $h = Loader::helper('concrete/dashboard'); ?>
<?
$tp1 = TaskPermission::getByHandle('access_task_permissions');
if ($tp1->can()) { 
	print $h->getDashboardPaneHeaderWrapper(t('Site Permissions'), false, false, false);
	$ih = Loader::helper('concrete/interface');
	$tps = array(
		TaskPermission::getByHandle('access_task_permissions'),
		TaskPermission::getByHandle('access_sitemap'),
		TaskPermission::getByHandle('access_user_search'),
		TaskPermission::getByHandle('access_group_search'),
		TaskPermission::getByHandle('access_page_defaults'),
		TaskPermission::getByHandle('install_packages'),
		TaskPermission::getByHandle('uninstall_packages'),
		TaskPermission::getByHandle('backup'),
		TaskPermission::getByHandle('sudo'),
		TaskPermission::getByHandle('delete_user')
	);
	$tpl = new TaskPermissionList();
	foreach($tps as $tp) {
		$tpl->add($tp);
	}
	?>
	
		<form method="post" id="ccm-task-permissions" action="<?=$this->url('/dashboard/system/permissions/tasks', 'save_task_permissions')?>">
		<?=$this->controller->token->output('update_permissions');?>
		<? print Loader::helper('concrete/dashboard/task_permissions')->getForm($tpl, t('Set administrative access details.')); ?>
		<div class="ccm-pane-footer">
			<? print $ih->submit(t('Save'), 'ccm-task-permissions', 'right', 'primary'); ?>
		</div>
		</form>
	<?=$h->getDashboardPaneFooterWrapper(false); ?>
<? } else { ?>
	<?=$h->getDashboardPaneHeaderWrapper(t('Site Permissions'));?>
	<?=t('You are not allowed to change these permissions.')?>
	<?=$h->getDashboardPaneFooterWrapper(); ?>

<? } ?>