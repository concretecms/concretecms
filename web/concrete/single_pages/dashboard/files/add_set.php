<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<? $ih = Loader::helper('concrete/interface'); ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Set'), false, 'span10 offset1', false)?>
    <form method="post" class="form-horizontal" id="file-sets-add" action="<?=$this->url('/dashboard/files/add_set', 'do_add')?>">
	<div class="ccm-pane-body">
    	
		<?=$validation_token->output('file_sets_add');?>

		<div class="control-group">
			<?=Loader::helper("form")->label('file_set_name', t('Name'))?>
			<div class="controls">
				<?=$form->text('file_set_name','', array('class' => 'span4'))?>
			</div>
		</div>
	</div>
	<div class="ccm-pane-footer">
			<?=Loader::helper("form")->submit('add', t('Add'), array('class' => 'ccm-button-right primary'))?>
	</div>
    </form>
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>