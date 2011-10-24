<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<? $ih = Loader::helper('concrete/interface'); ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Set'), false, false, false)?>
	<div class="ccm-pane-body">
	<form method="post" id="file-sets-add" action="<?=$this->url('/dashboard/files/add_set', 'do_add')?>">
		<?=$validation_token->output('file_sets_add');?>

		<div class="clearfix">
			<?=Loader::helper("form")->label('file_set_name', t('Name'))?>
			<div class="input">
				<?=$form->text('file_set_name','', array('class' => 'span6'))?>
			</div>
		</div>
	</form>
	</div>
	<div class="ccm-pane-footer">
			<?=Loader::helper("form")->submit('add', t('Add File Set'), array('class' => 'primary'))?>
	</div>
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>