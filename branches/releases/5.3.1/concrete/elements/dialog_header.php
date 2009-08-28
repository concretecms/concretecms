<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<script type="text/javascript">
var ccm_currentDialog = "<?php echo $_SERVER['REQUEST_URI']?>";
// add validation submit to any forms w/validation
$(function() {
	ccm_blockFormInit();
	ccm_activateFileSelectors();	
});
</script>
<div id="ccm-dialog-throbber"><img src="<?php echo ASSETS_URL_IMAGES?>/throbber_white_32.gif" width="32" height="32" /></div>

<div class="ccm-pane-controls">