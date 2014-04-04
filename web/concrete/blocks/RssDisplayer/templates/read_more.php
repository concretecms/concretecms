<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div id="rssSummaryList<?=intval($bID)?>" class="rssSummaryList">

<? if( strlen($title)>0 ){ ?>
	<div class="rssSummaryListTitle" style="margin-bottom:8px"><?=$title?></div>
<? } ?>

<? 
$rssObj=$controller;
$textHelper = Loader::helper("text"); 

if (!$dateFormat) {
	$dateFormat = t('F jS');
}

if( strlen($errorMsg)>0 ){
	echo $errorMsg;
}else{

	foreach($posts as $itemNumber=>$item) { 
	
		if( intval($itemNumber) >= intval($rssObj->itemsToDisplay) ) break;
		?>
		
		<div class="rssItem">
		<h3><?= $item->get_title(); ?></h3>
		<h5><?= $item->get_date($dateFormat); ?></h5>
			<div class="rssItemSummary"><p>
				<?
				if( $rssObj->showSummary ){
					echo $textHelper->shortText( strip_tags($item->get_description()), 190 );
				}
				?>
			</p>
			<p><a href="<?= $item->get_permalink(); ?>"><?=t('Read More')?></a></p>
			</div>
		</div>
	
<?  }  
}
?>
</div>