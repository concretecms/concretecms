<?
defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
$ci = Loader::helper('concrete/urls');
$ch = Loader::helper('concrete/interface');
$tp = new TaskPermission();
if ($tp->canInstallPackages()) {
	$mi = Marketplace::getInstance();
}
?>

<div class="ccm-ui">
<div class="row">


<? if ($_GET['_ccm_dashboard_external']) { ?>
	<div id="newsflow">
	<ul class="ccm-pane-header-icons">
		<li><a href="javascript:void(0)" onclick="ccm_closeNewsflow()" class="ccm-icon-close"><?=t('Close')?></a></li>
	</ul>
<? } else { ?>
	<div class="ccm-pane" id="ccm-marketplace-item-browser">
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeader(t('Browse Themes'), t('Get more themes from concrete5.org.'));?>
<? } ?>
<div class="ccm-pane-body" id="ccm-marketplace-detail" <? if ($list->getTotal() == 0) { ?> style="display: block" <? } ?>>
<div id="ccm-marketplace-detail-inner" ></div>
<? if ($list->getTotal() > 0) { ?>
<p class="ccm-marketplace-detail-loading"><?=t('Loading Details')?></p>
<? } else { ?>
	<p><?=t('No results found.')?></p>
<? } ?>
<div id="newsflow-paging-previous"><span><a href="javascript:void(0)" onclick="ccm_marketplaceBrowserSelectPrevious()"></a></span></div>
<div id="newsflow-paging-next"><span><a href="javascript:void(0)" onclick="ccm_marketplaceBrowserSelectNext()"></a></span></div>

</div>

<? if ($tp->canInstallPackages()) { ?>
<div class="ccm-pane-options">
<div class="ccm-pane-options-permanent-search">
<form id="ccm-marketplace-browser-form" method="get" action="<?=$this->url('/dashboard/extend/themes')?>">
	<?=Loader::helper('form')->hidden('_ccm_dashboard_external')?>
	<div class="span4">
	<?=$form->label('marketplaceRemoteItemKeywords', t('Keywords'))?>
	<div class="input">
		<?=$form->text('marketplaceRemoteItemKeywords', array('style' => 'width: 140px'))?>
	</div>
	</div>
	
	<div class="span4">
	<?=$form->label('marketplaceRemoteItemSetID', t('Category'))?>
	<div class="input">
	<?=$form->select('marketplaceRemoteItemSetID', $sets, $selectedSet, array('style' => 'width: 150px'))?>
	</div>
	</div>

	<div class="span4">
	<?=$form->label('marketplaceRemoteItemSortBy', t('Sort By'))?>
	<div class="input">
	<?=$form->select('marketplaceRemoteItemSortBy', $sortBy, $selectedSort, array('style' => 'width: 150px'))?>
	</div>
	</div>
	
	<div class="span2">
		<?=$form->submit('submit', t('Search'))?>
	</div>
</form>	
</div>
</div>
<? } ?>

<div class="ccm-pane-body">
	<? if (!$tp->canInstallPackages()) { ?>
		<div class="alert-message block-message error">
			<p><?=t('You do not have access to download themes or add-ons from the marketplace.')?></p>
		</div>
	<? } else if (!$mi->isConnected()) { ?>
		<? Loader::element('dashboard/marketplace_connect_failed')?>
	<? } else {

		$pagination = $list->getPagination();
		?>
	
		<table class="ccm-marketplace-results">
			<tr>
			<?php 
			$numCols=3;
			$colCount=0;
			foreach($items as $item){ 
				if($colCount==$numCols){
					echo '</tr><tr>';
					$colCount=0;
				}
				?>
				<td valign="top" width="33%" mpID="<?=$item->getMarketplaceItemID()?>" class="ccm-marketplace-item ccm-marketplace-item-unselected"> 
				
				<img class="ccm-marketplace-item-thumbnail" width="44" height="44" src="<?php echo $item->getRemoteIconURL() ?>" />
				<div class="ccm-marketplace-results-info">
					<h4><?=$item->getName()?></h4>
					<h5><?=((float) $item->getPrice() == 0) ? t('Free') : $item->getPrice()?></h5>
					<p><?php echo $item->getDescription() ?></p>
				</div>
					
				</td>
			<?php   $colCount++;
			}
			for($i=$colCount;$i<$numCols;$i++){
				echo '<td>&nbsp;</td>'; 
			} 
			?>
			</tr>
		</table>
	<? } ?>
</div>

<? $url = Loader::helper('url')->unsetVariable('prev'); ?>
<div class="ccm-pane-footer" id="ccm-marketplace-browse-footer"><?=$list->displayPaging($url)?></div>


</div>

</div>
</div>

<script type="text/javascript">
$(function() {
	ccm_marketplaceBrowserInit(false, <? if ($_REQUEST['prev'] == 1) { ?>'last'<? } else { ?>false<? } ?>);
});
</script>