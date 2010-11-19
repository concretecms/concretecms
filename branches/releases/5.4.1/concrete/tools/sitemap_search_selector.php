<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied. You do not have access to sitemap permissions.'));
}

?>
<div>

<?php echo Loader::helper('html')->css('ccm.sitemap.css')?>
<?php echo Loader::helper('html')->javascript('ccm.sitemap.js')?>


<script type="text/javascript">
var ccm_ssActiveTab = "ccm-show-sitemap";

$("#ccm-ss-tabs a").click(function() {
	$("li.ccm-nav-active").removeClass('ccm-nav-active');
	$("#" + ccm_ssActiveTab + "-tab").hide();
	ccm_ssActiveTab = $(this).attr('id');
	$(this).parent().addClass("ccm-nav-active");
	$("#" + ccm_ssActiveTab + "-tab").show();
});

</script>

<ul class="ccm-dialog-tabs" id="ccm-ss-tabs">
<li class="ccm-nav-active"><a href="javascript:void(0)" id="ccm-show-sitemap"><?php echo t('Sitemap')?></a></li>
<li><a href="javascript:void(0)" id="ccm-show-search"><?php echo t('Search')?></a></li>
</ul>

<br/>

<div id="ccm-show-sitemap-tab">
<?php  $sitemapCombinedMode = true; ?>
<?php  include(DIR_FILES_TOOLS_REQUIRED . '/sitemap_overlay.php'); ?>

</div>

<div id="ccm-show-search-tab" style="display: none">

<?php  
$sitemap_select_mode = $select_mode;
include(DIR_FILES_TOOLS_REQUIRED . '/pages/search_dialog.php'); ?>


</div>
</div>