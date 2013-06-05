<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<table class="ccm-marketplace-results">
	<tr>
	<?php 
	$numCols=3;
	$colCount=0;
	$i = 0;
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
			<? $thumb = $item->getLargeThumbnail(); ?>
			<? if ($thumb && $type == 'themes') { ?>
				
				<div class="ccm-marketplace-results-image-hover ccm-ui"><ul class="media-grid"><li><a href="javascript:void(0)"><img src="<?=$thumb->src?>" width="<?=$thumb->width?>" height="<?=$thumb->height?>" /></a></li></ul></div>
				
			
			<?
			} ?>
		</div>
			
		</td>
	<?php   $colCount++;
	$i++;
	}
	for($i=$colCount;$i<$numCols;$i++){
		echo '<td>&nbsp;</td>'; 
	} 
	?>
	</tr>
</table>
