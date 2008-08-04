<style>
.rssSummaryList .rssItem{ margin-bottom:16px }
.rssSummaryList .rssItem .rssItemTitle{ font-weight:bold }
.rssSummaryList .rssItem .rssItemDate{ color:#999999 }
.rssSummaryList .rssItem .rssItemSummary{}
.rssSummaryList .rssSummaryListTitle{font-weight:bold}
</style>

<div id="rssSummaryList<?=intval($survey->questionSetId)?>" class="rssSummaryList">

<? if( strlen($title)>0 ){ ?>
	<div class="rssSummaryListTitle" style="margin-bottom:8px"><?=$title?></div>
<? } ?>

<? 
$rssObj=$controller;
$textHelper = Loader::helper("text"); 

if( strlen($errorMsg)>0 ){
	echo $errorMsg;
}else{

	foreach($posts as $itemNumber=>$item) { 
	
		if( intval($itemNumber) >= intval($rssObj->itemsToDisplay) ) break;
		?>
		
		<div class="rssItem">
			<div class="rssItemTitle"> 
				<a href="<?= $item->get_permalink(); ?>" <? if($rssObj->launchInNewWindow) echo 'target="_blank"' ?> >
					<?= $item->get_title(); ?>
				</a>
			</div>
			<div class="rssItemDate"><?= $item->get_date('F jS'); ?></div>
			<div class="rssItemSummary">
				<?
				if( $rssObj->showSummary ){
					echo $textHelper->shortText( strip_tags($item->get_description()) );
				}
				?>
			</div>
		</div>
	
<?  }  
}
?>
</div>