<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<style>
.rssSummaryList .rssItem{ margin-bottom:16px }
.rssSummaryList .rssItem .rssItemTitle{ font-weight:bold }
.rssSummaryList .rssItem .rssItemDate{ color:#999999 }
.rssSummaryList .rssItem .rssItemSummary{}
.rssSummaryList .rssSummaryListTitle{font-weight:bold}
</style>

<div id="rssSummaryList<?php echo intval($survey->questionSetId)?>" class="rssSummaryList">

<?php  if( strlen($title)>0 ){ ?>
	<div class="rssSummaryListTitle" style="margin-bottom:8px"><?php echo $title?></div>
<?php  } ?>

<?php  
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
				<a href="<?php echo  $item->get_permalink(); ?>" <?php  if($rssObj->launchInNewWindow) echo 'target="_blank"' ?> >
					<?php echo  $item->get_title(); ?>
				</a>
			</div>
			<div class="rssItemDate"><?php echo  $item->get_date('F jS'); ?></div>
			<div class="rssItemSummary">
				<?php 
				if( $rssObj->showSummary ){
					echo $textHelper->shortText( strip_tags($item->get_description()) );
				}
				?>
			</div>
		</div>
	
<?php   }  
}
?>
</div>