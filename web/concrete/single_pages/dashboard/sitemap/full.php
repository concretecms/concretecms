<?
defined('C5_EXECUTE') or die("Access Denied.");
$sh = Loader::helper('concrete/dashboard/sitemap');

if (isset($_REQUEST['reveal'])) {
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
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Sitemap'), t('The sitemap allows you to view your site as a tree and easily organize its hierarchy.'), 'span10 offset1', false);?>
<div class="ccm-pane-options">
	<a href="javascript:void(0)" onclick="ccm_paneToggleOptions(this)" class="ccm-icon-option-<? if ($_SESSION['dsbSitemapShowSystem'] == 1) { ?>open<? } else { ?>closed<? } ?>"><?=t('Options')?></a>
	<div class="ccm-pane-options-content" <? if ($_SESSION['dsbSitemapShowSystem'] == 1) { ?> style="display: block" <? } ?>>
		<form>
		<div id="ccm-show-all-pages" class="clearfix">
			<label for="ccm-show-all-pages-cb"><?=t('Show System Pages')?></label>
			<div class="input">
			<ul class="inputs-list">
				<li><input type="checkbox" id="ccm-show-all-pages-cb" <? if ($_SESSION['dsbSitemapShowSystem'] == 1) { ?> checked <? } ?> /></li>
			</ul>		
			</div>
		</div>
		</form>
	</div>
</div>
<div class="ccm-pane-body ccm-pane-body-footer">

	<? $u = new User();
	if ($u->isSuperUser()) {
		if (Queue::exists('copy_page')) {
		$q = Queue::get('copy_page');
		if ($q->count() > 0) { ?>

			<div style="position: relative" class="alert alert-warning">
				<div style="position: absolute; top: 5px; right: 5px"><button class="btn btn-small" onclick="ccm_refreshCopyOperations()"><?=t('Resume Copy')?></button></div>
				<?=t('Page copy operations pending.')?>
			</div>

		<? }
	}

	} ?>

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
<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>