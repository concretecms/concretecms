<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<script>
<? 

$toggleQuestionsShowText = t('View all fields').' &raquo;'; 
$toggleQuestionsHideText = t('Hide fields') . ' &raquo;'; 

?>
var toggleQuestionsShowText='<?=$toggleQuestionsShowText?>';
var toggleQuestionsHideText='<?=$toggleQuestionsHideText?>';
function toggleQuestions(qsID,trigger){
	$('.extraQuestionRow'+qsID).toggleClass('noDisplay');
	if(trigger.state=='open') {
		 trigger.innerHTML = toggleQuestionsShowText;
		 trigger.state='closed';
	}else{
		trigger.state='open';
		trigger.innerHTML = toggleQuestionsHideText;
	}
}
</script> 

<h1><span><?=t('Form Results')?></span></h1>

<div class="ccm-dashboard-inner">

<? if (count($surveys) == 0) { ?>
<?=t('You have not created any forms.')?>
<? } else { ?>

<div style="margin:0px; padding:0px; width:100%; height:auto" >
<table class="entry-form" >
	<tr>
		<td class="header"><?=t('Form')?></td>
		<td class="header"><?=t('Options')?></td>
	</tr>
	<? foreach($surveys as $thisQuestionSetId=>$survey){
		$b=Block::getByID( intval($survey['bID']) );
		if (is_object($b)) {
			$oc = $b->getBlockCollectionObject();
			$ocID = $oc->getCollectionID();		
			?>
			<tr>
				<td><?=$survey['surveyName']?></td>
				<td>
					<a href="<?=DIR_REL?>/index.php?cID=<?=$c->getCollectionId()?>&qsid=<?=$thisQuestionSetId?>"><?=t('View Responses')?></a>
					|
					<a href="<?=DIR_REL?>/index.php?cID=<?=$ocID?>"><?=t('Open Page')?></a>	
				</td>
			</tr>
		<? }
		
	}?>
</table>
</div>

<? } ?>

</div>



<? if( strlen($questionSet)>0 ){ ?>

	<a name="responses" id="responses"></a>	
	<h1><span><?=t('Responses to')?> "<?=$surveys[$questionSet]['surveyName']?>"</span></h1>
	<div class="ccm-dashboard-inner">
	
	<? if( count($answerSets)==0 ){ ?>
		<div><?=t('No one has yet submitted this form.')?></div>
	<? }else{ ?>
	
		<div style="margin-bottom:8px">
			<div style="float:right; margin-bottom:8px">
			<a href="<?=$this->url('/dashboard/reports/forms/', 'excel', '?qsid=' . $questionSet)?>"><?=t('Export to Excel')?> &raquo;</a>
			</div>
			
			<? if($_REQUEST['all']!=1){ ?>
				<a href="<?=$this->url('/dashboard/reports/forms/', 'view', '?all=1&sortBy='.$_REQUEST['sortBy'].'&qsid='.$questionSet)?>"><?=t('Show All')?></a>
			<? }else{ ?>
				<a href="<?=$this->url('/dashboard/reports/forms/', 'view', '?all=0&sortBy='.$_REQUEST['sortBy'].'&qsid='.$questionSet)?>"><?=t('Show Paging')?></a>
			<? } ?>
			
			&nbsp;|&nbsp;
			 
			<? if($_REQUEST['sortBy']=='chrono'){ ?>
				<a href="<?=$this->url('/dashboard/reports/forms/', 'view', '?all=1&sortBy=newest&qsid='.$questionSet)?>"><?=t('Sort by Newest')?></a>
			<? }else{ ?>
				<a href="<?=$this->url('/dashboard/reports/forms/', 'view', '?all=0&sortBy=chrono&qsid='.$questionSet)?>"><?=t('Sort Chronologically')?></a>
			<? } ?>			
			<div class="spacer"></div>
		</div>
	
		<? foreach($answerSets as $answerSetId=>$answerSet){ ?>
			
			<div style="margin:0px; padding:0px; width:100%; height:auto" >
			<table class="entry-form" width="100%" style="margin-bottom:2px">
				<tr>
					<td class="header"><?=t('Submitted Date')?></td>
					<td class="header"><?=$answerSet['created']?></td>
				</tr>
				<? 
				$questionNumber=0;
				$numQuestionsToShow=2;
				foreach($questions as $questionId=>$question){ 
					$questionNumber++; 
					?>
					<tr class="<?=($questionNumber>$numQuestionsToShow)?'extra':''?>QuestionRow<?=$answerSetId?> <?=($questionNumber>$numQuestionsToShow)?'noDisplay':'' ?>">
						<td width="33%">
							<?= $questions[$questionId]['question'] ?>
						</td>
						<td>
							<?
							if( $question['inputType']=='fileupload' ){
								$fID=intval($answerSet['answers'][$questionId]['answer']);
								$file=File::getByID($fID);
								if($fID && $file){
									$fileVersion=$file->getApprovedVersion();
									echo '<a href="'. DIR_REL . $fileVersion->getRelativePath() .'">'.$fileVersion->getFileName().'</a>';
								}else{
									echo t('File not found');
								}
							}elseif($question['inputType']=='text'){
								echo $answerSet['answers'][$questionId]['answerLong'];
							}else{
								echo $answerSet['answers'][$questionId]['answer'];
							}
							?>							
						</td>
					</tr>
				<? } ?>
			</table>
			</div>
			
			<? if( count($questions)>$numQuestionsToShow ){ ?>
				<div style="text-align:right; margin-bottom:16px"><a onclick="toggleQuestions(<?=$answerSetId?>,this)"><?=$toggleQuestionsShowText?></a></div>
			<? } ?>
			
		<? } ?>
		
	<? } ?> 	

	<? if($paginator && strlen($paginator->getPages())>0){ ?>	 
		 <div  class="pagination">
			 <div class="pageLeft"><?=$paginator->getPrevious()?></div>
			 <div class="pageRight"><?=$paginator->getNext()?></div>
			 <?=$paginator->getPages()?>
		 </div>		
	<? } ?>		
	
	</div>

<? } ?>