<? defined('C5_EXECUTE') or die("Access Denied.");?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Site Name'), false, 'span12 offset2', false)?>
<form method="post" id="site-form" action="<?=$this->action('update_sitename')?>">
<div class="ccm-pane-body">
	<?=$this->controller->token->output('update_sitename')?>
	<div class="clearfix">
	<?=$form->label('SITE', t('Site Name'))?>
	<div class="input">
	<?=$form->text('SITE', $site, array('class' => 'span8'))?>
	</div>
	</div>
</div>
<div class="ccm-pane-footer">
	<?
	print $interface->submit(t('Save'), 'site-form', 'right','primary');
	?>
</div>
</form>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
