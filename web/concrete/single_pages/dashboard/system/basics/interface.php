<? defined('C5_EXECUTE') or die("Access Denied.");?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Interface Settings'), false, 'span12 offset2', false)?>
<form method="post" action="<?=$this->action('save_interface_settings')?>" enctype="multipart/form-data" >
<div class="ccm-pane-body">
<?=Loader::helper('validation/token')->output('save_interface_settings')?>

<? if (!defined('WHITE_LABEL_DASHBOARD_BACKGROUND_FEED') && !defined('WHITE_LABEL_DASHBOARD_BACKGROUND_SRC')) { ?>

<fieldset>

<legend><?=t('Dashboard')?></legend>
<div class="clearfix">
<label><?=t('Background Image')?></label>
<div class="input">
<ul class="inputs-list">
	<li><label><?=$form->radio('DASHBOARD_BACKGROUND_IMAGE', '', $DASHBOARD_BACKGROUND_IMAGE)?> <span><?=t('Pull a picture of the day from concrete5.org (Default)')?></span></label></li>
	<li><label><?=$form->radio('DASHBOARD_BACKGROUND_IMAGE', 'none', $DASHBOARD_BACKGROUND_IMAGE)?> <span><?=t('None')?></span></label></li>
	<li><label><?=$form->radio('DASHBOARD_BACKGROUND_IMAGE', 'custom', $DASHBOARD_BACKGROUND_IMAGE)?> <span><?=t('Specify Custom Image')?></span></label>
	
	<div id="custom-background-image" <? if ($DASHBOARD_BACKGROUND_IMAGE != 'custom') { ?>style="display: none" <? } ?>>
		<br/>
		<?=Loader::helper('concrete/asset_library')->image('DASHBOARD_BACKGROUND_IMAGE_CUSTOM_FILE_ID', DASHBOARD_BACKGROUND_IMAGE_CUSTOM_FILE_ID, t('Choose Image'), $imageObject)?>
	</div>
	
	</li>
</ul>
</fieldset>


<script type="text/javascript">
$(function() {
	$("input[name=DASHBOARD_BACKGROUND_IMAGE]").change(function() {
		if ($("input[name=DASHBOARD_BACKGROUND_IMAGE]:checked").val() == 'custom') { 
			$("#custom-background-image").show();
		} else {
			$("#custom-background-image").hide();
		}
	});
});
</script>

<? } ?>

<fieldset>

<legend><?=t('Toolbar')?></legend>
<div class="clearfix">
<label><?=t('Quick Nav Behavior')?></label>
<div class="input">
<ul class="inputs-list">
	<li><label><?=$form->radio('TOOLBAR_QUICK_NAV_BEHAVIOR', '', $TOOLBAR_QUICK_NAV_BEHAVIOR)?> <span><?=t('Show quick navigation bar when hovering over edit bar (default).')?></span></label></li>
	<li><label><?=$form->radio('TOOLBAR_QUICK_NAV_BEHAVIOR', 'always', $TOOLBAR_QUICK_NAV_BEHAVIOR)?> <span><?=t('Always show quick navigation bar.')?></span></label></li>
	<li><label><?=$form->radio('TOOLBAR_QUICK_NAV_BEHAVIOR', 'disabled', $TOOLBAR_QUICK_NAV_BEHAVIOR)?> <span><?=t('Disable quick navigation bar.')?></span></label></li>
</ul>
</fieldset>

</div>
<div class="ccm-pane-footer">
	<?=Loader::helper('concrete/interface')->submit(t('Save'), 'submit', 'right', 'primary')?>
</div>
</form>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
