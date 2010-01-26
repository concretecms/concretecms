<?
defined('C5_EXECUTE') or die(_("Access Denied."));

/*

if ($_REQUEST['mode'] == 'users') {
	$displayGroups = false;
} else if ($_REQUEST['mode'] == 'groups') {
	$displayUsers = false;
}

$c1 = Page::getByPath('/dashboard/users');
$cp1 = new Permissions($c1);
$c2 = Page::getByPath('/dashboard/users/groups');
$cp2 = new Permissions($c2);
if ((!$cp1->canRead()) && (!$cp2->canRead())) {
	die(_("Access Denied."));
}
*/

?>
<div>

<link href="<?=ASSETS_URL_CSS?>/ccm.sitemap.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/ccm.sitemap.js"></script>


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
<li class="ccm-nav-active"><a href="javascript:void(0)" id="ccm-show-sitemap"><?=t('Sitemap')?></a></li>
<li><a href="javascript:void(0)" id="ccm-show-search"><?=t('Search')?></a></li>
</ul>

<br/>

<div id="ccm-show-sitemap-tab">
<? $sitemapCombinedMode = true; ?>
<? include(DIR_FILES_TOOLS_REQUIRED . '/sitemap_overlay.php'); ?>

</div>

<div id="ccm-show-search-tab" style="display: none">

<? 
include(DIR_FILES_TOOLS_REQUIRED . '/pages/search_dialog.php'); ?>


</div>
</div>