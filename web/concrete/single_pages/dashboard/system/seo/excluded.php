<?php defined('C5_EXECUTE') or die('Access Denied');
$form = Loader::helper('form');
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Reserved Words'), t("Words listed here will be automatically removed from url slugs."), false, false); ?>

<form method="post" id="url-form" action="<?php echo $this->action('save')?>">
	<div class="ccm-pane-body">
		<div class="control-group">
			<textarea style='width:100%;height:100px' name='SEO_EXCLUDE_WORDS'><?=$SEO_EXCLUDE_WORDS?></textarea>
			<span class='help-block'><?=t('Separate reserved words with a comma.')?></span>
		</div>
	</div>
	<div class="ccm-pane-footer">
		<?php echo $interface->submit(t('Save'), null, 'right', 'primary');?>
	</div>
</form>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();
