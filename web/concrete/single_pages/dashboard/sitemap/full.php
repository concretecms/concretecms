<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::library('search');

$this->addHeaderItem(Loader::helper('html')->css('ccm.sitemap.css'));
$this->addHeaderItem(Loader::helper('html')->javascript('ccm.sitemap.js', false, true));

$sh = Loader::helper('concrete/dashboard/sitemap');

if (isset($_REQUEST['reveal'])) {
	$nc = new Collection($_REQUEST['reveal']);
	$nc = Page::getByID($_REQUEST['reveal']);
	$nh = Loader::helper('navigation');
	$cArray = $nh->getTrailToCollection($nc);
	foreach($cArray as $co) {
		ConcreteDashboardSitemapHelper::addOpenNode($co->getCollectionID());
	}
	ConcreteDashboardSitemapHelper::addOneTimeActiveNode($_REQUEST['reveal']);
}

$nodes = $sh->getSubNodes(0, 1, false, true);
$instanceID = time();
$listHTML = $sh->outputRequestHTML($instanceID, 'full', false, $nodes);

?>

<script type="text/javascript">
	var CCM_LAUNCHER_SITEMAP = 'full';
	head.ready(function() {
		ccmSitemapLoad('<?=$instanceID?>', 'full');
	});
</script>

<h1><span><?=t('Sitemap')?></span></h1>

<div class="ccm-dashboard-inner" >

	<? if ($sh->canRead()) { ?>
	
		<div id="ccm-sitemap-message"></div>
	
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td style="width: 100%" valign="top">
		
		<div id="tree" sitemap-instance-id="<?=$instance_id?>">
			<ul id="tree-root0" tree-root-node-id="0" sitemap-mode="full" sitemap-instance-id="<?=$instanceID?>">
			<?=$listHTML?>
			</ul>
		</div>
	
		</td>
		<td valign="top">
		
		<div id="ccm-show-all-pages">
		<input type="checkbox" id="ccm-show-all-pages-cb" <? if ($_SESSION['dsbSitemapShowSystem'] == 1) { ?> checked <? } ?> />
		<label for="ccm-show-all-pages-cb"><?=t('Show System Pages')?></label>
		</div>
		
		</td>
		</tr>
		</table>
		
		</div>
	
	<? } else { ?>
	
		<p><?=t("You do not have access to the sitemap.");?></p>
	
	<? } ?>
	
</div>