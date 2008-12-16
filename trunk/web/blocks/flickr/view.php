<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<style>

.photoSummaryList .photoItem{ margin-bottom:16px; text-align:center }
.photoSummaryList .photoItem .photoItemTitle{ }
.photoSummaryList .photoItem .photoItemDate{ color:#999999; display:none; }
.photoSummaryList .photoItem .photoItemSummary{}
.photoSummaryList .photoSummaryListTitle{ font-weight:bold }
</style>

<div id="photoSummaryList<?=intval($survey->questionSetId)?>" class="photoSummaryList">

<? if( strlen($title)>0 ){ ?>
	<div class="photoSummaryListTitle" style="margin-bottom:8px"><?=$title?></div>
<? } ?>

<? 
$controllerObj=$controller;
$textHelper = Loader::helper("text"); 
//Loader::block('library_file');

if( strlen($errorMsg)>0 ){ 
	echo $errorMsg;
}else{

	foreach($posts as $itemNumber=>$item) { 	
		if( intval($itemNumber) >= intval($controllerObj->itemsToDisplay) ) break; 
		//$fileURL=$item->get_link(0,"enclosure");
		$enclosure=$item->get_enclosure(); 
		$fileURL=$enclosure->link; 
		$fileID=$item->get_id(); 
		if(!$fileID) $fileID=$fileURL;
		$fileCachePathRel=$controllerObj->getResizedImagePath($fileURL, $fileID); 
		?>
		
		<div class="photoItem">
			<div class="photoItemDate"><?= $item->get_date('F jS'); ?></div>
			<div class="photoImgWrap"> 
				<? if($fileCachePathRel){ ?>
				<img src="<?=$fileCachePathRel?>" />
				<? }else{ ?>
				Image not found.
				<? } ?>
			</div>
			<div class="photoItemTitle"> 
				<a href="<?= $item->get_permalink(); ?>" <? if($controllerObj->launchInNewWindow) echo 'target="_blank"' ?> >
					<?= $item->get_title(); ?>
				</a>
			</div>			
		</div>
	
<?  }  
}
?>
</div>