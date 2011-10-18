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

<div class="ccm-pane">
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeader(t('Browse Themes'), t('Get more themes from concrete5.org.'));?>
<div class="ccm-pane-body" id="ccm-marketplace-detail">
<div id="ccm-marketplace-detail-inner"></div>
<p class="ccm-marketplace-detail-loading"><?=t('Loading Details')?></p>
</div>

<? if ($tp->canInstallPackages()) { ?>
<div class="ccm-pane-options">
<div class="ccm-pane-options-permanent-search">
<form method="get" action="<?=$this->url('/dashboard/extend/themes')?>">
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

<div class="ccm-pane-body ccm-pane-body-footer">
	<? if (!$tp->canInstallPackages()) { ?>
		<div class="alert-message block-message error">
			<p><?=t('You do not have access to download themes or add-ons from the marketplace.')?></p>
		</div>
	<? } else if (!$mi->isConnected()) { ?>
		<? Loader::element('dashboard/marketplace_connect_failed')?>
	<? } else { ?>

		<? if ($list->getTotal() > 0) { ?>
		
				
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
					
						<? /* 
						<div><? if ($type == 'themes') { ?><a title="<?php echo t('Preview')?>" onclick="ccm_previewMarketplaceTheme(1, <?php echo intval($item->getRemoteCollectionID())?>,'<?php echo addslashes($item->getName()) ?>','<?php echo addslashes($item->getHandle()) ?>')" 
							href="javascript:void(0)" class="preview"><? } ?><img style="margin-bottom: 8px" src="<?php echo $item->getRemoteIconURL() ?>" /><? if ($type == 'themes') { ?></a><? } ?></div>
						<h2><?php echo $item->getName() ?>
						<? if ($type == 'themes') { ?>
						<a title="<?php echo t('Preview')?>" onclick="ccm_previewMarketplaceTheme(1, <?php echo intval($item->getRemoteCollectionID())?>,'<?php echo addslashes($item->getName()) ?>','<?php echo addslashes($item->getHandle()) ?>')" 
							href="javascript:void(0)" class="preview"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/magnifying.png" alt="<?php echo t('Preview')?>" /></a>
						<? } ?>
						</h2>						
						<div><?php echo $item->getDescription() ?></div>
						<div style="margin-top: 8px"><strong><?=t('Price')?></strong> <?=((float) $item->getPrice() == 0) ? t('Free!') : $item->getPrice()?></div>
						<div style="margin-top: 8px">
						<?=$ch->button_js(t('More Information'), 'window.open(\'' . $item->getRemoteURL() . '\')', 'left');?>
						<?=$ch->button_js($buttonText, $buttonAction, 'left')?>
						</div>
						*/ ?>
						
					</td>
				<?php   $colCount++;
				}
				for($i=$colCount;$i<$numCols;$i++){
					echo '<td>&nbsp;</td>'; 
				} 
				?>
				</tr>
			</table>
		<? } else { ?>
			<p><?=t('No results found.')?></p>
		<? } ?>
	
	

	<? } ?>
</div>
</div>

</div>
</div>

<script type="text/javascript">
$(function() {
	$(".ccm-marketplace-item").click(function() {
		$("#ccm-marketplace-detail-inner").hide();
		$('.ccm-marketplace-detail-loading').show();	

		var mpID = $(this).attr('mpID');
		$('.ccm-marketplace-item-selected').removeClass('ccm-marketplace-item-selected').addClass('ccm-marketplace-item-unselected');
		$(this).removeClass('ccm-marketplace-item-unselected').addClass('ccm-marketplace-item-selected');
		$('#ccm-marketplace-detail').show();
		$('#ccm-marketplace-detail-inner').load(CCM_TOOLS_PATH + '/marketplace/details', {
			'mpID': mpID
		}, function() {
			window.scrollTo(0,0);
			$('.ccm-marketplace-detail-loading').hide();
			$("#ccm-marketplace-detail-inner").show();
			if ($(".ccm-marketplace-item-information-inner").height() < 380) {
				$(".ccm-marketplace-item-information-more").hide();
			}
			$("#ccm-marketplace-item-screenshots").nivoSlider({
				'controlNav': false,
				'effect': 'fade',
				'pauseOnHover': false,
				'directionNav': false
			});

		});
	});
});
</script>