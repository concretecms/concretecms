<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::library('search');
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
	$(function() {
		ccmSitemapLoad('<?=$instanceID?>', 'full');
	});
</script>

<div class="row">
<div class="span14 offset1 columns">

<div class="ccm-dashboard-pane">
<?=$this->controller->outputDashboardPaneHeader(t('Sitemap'));?>
<div class="ccm-dashboard-pane-options">
	<div id="ccm-show-all-pages">
		<label for="ccm-show-all-pages-cb"><?=t('Show System Pages')?></label>
		<div class="input">
		<ul class="inputs-list">
			<li><input type="checkbox" id="ccm-show-all-pages-cb" <? if ($_SESSION['dsbSitemapShowSystem'] == 1) { ?> checked <? } ?> /></li>
		</ul>		
		</div>
	</div>
</div>

<div class="ccm-dashboard-pane-body">
	<? if ($sh->canRead()) { ?>
	
		<div id="ccm-sitemap-message"></div>
	
		
		<div id="tree" sitemap-instance-id="<?=$instance_id?>">
			<ul id="tree-root0" tree-root-node-id="0" sitemap-mode="full" sitemap-instance-id="<?=$instanceID?>">
			<?=$listHTML?>
			</ul>
		</div>
		
	
	<? } else { ?>
	
		<p><?=t("You do not have access to the sitemap.");?></p>
	
	<? } ?>
</div>



</div>
</div>
</div>