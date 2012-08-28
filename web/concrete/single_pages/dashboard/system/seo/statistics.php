<?php defined('C5_EXECUTE') or die('Access Denied');
$form = Loader::helper('form');
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Statistics'), t("Tracks page views in concrete5. Disabling this may increase site performance, but you will have to get statistics information from elsewhere."), 'span6 offset3', false); ?>

	<form method="post" id="url-form" action="<?php echo $this->action('')?>">
		<?=$this->controller->token->output('update_statistics')?>
		<div class="ccm-pane-body">
			<div class="control-group">
				<label></label>
				<div class="controls">
				<label class="checkbox">
				<?=$form->checkbox('STATISTICS_TRACK_PAGE_VIEWS', 1, STATISTICS_TRACK_PAGE_VIEWS); ?>
				<span><?=t('Track page view statistics.');?></span>
				</label>
				</div>
			</div>
		</div>
		<div class="ccm-pane-footer">
			<?php echo $interface->submit(t('Save'), null, 'right', 'primary');?>
		</div>
	</form>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();
