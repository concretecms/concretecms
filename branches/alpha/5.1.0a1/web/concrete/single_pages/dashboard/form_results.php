<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<script>
<? $toggleQuestionsShowText='View all fields &raquo;' ?>
var toggleQuestionsShowText='<?=$toggleQuestionsShowText?>';
var toggleQuestionsHideText='Hide fields &raquo;';
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

<h1><span>Form Results</span></h1>

<div class="ccm-dashboard-inner">

<? if (count($surveys) == 0) { ?>
You have not created any forms.
<? } else { ?>

<div style="margin:0px; padding:0px; width:100%; height:auto" >
<table class="entry-form" >
	<tr>
		<td class="header">Survey</td>
		<td class="header">Options</td>
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
					<a href="<?=DIR_REL?>/index.php?cID=<?=$c->getCollectionId()?>&qsid=<?=$thisQuestionSetId?>">View Responses</a>
					|
					<a href="<?=DIR_REL?>/index.php?cID=<?=$ocID?>">Open Page</a>	
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
	<h1><span>Responses to "<?=$surveys[$questionSet]['surveyName']?>"</span></h1>
	<div class="ccm-dashboard-inner">
	
	<? if( count($answerSets)==0 ){ ?>
		<div>No one has yet submitted this form.</div>
	<? }else{ ?>
	
		<div style="margin-bottom:8px">
			<div style="float:right; margin-bottom:8px">
			<a href="<?=$this->url('/dashboard/form_results/', 'excel', '?qsid=' . $questionSet)?>">Export to Excel &raquo;</a>
			</div>
			
			<? if($_REQUEST['all']!=1){ ?>
				<a href="<?=$this->url('/dashboard/form_results/', 'view', '?all=1&sortBy='.$_REQUEST['sortBy'].'&qsid='.$questionSet)?>">Show All</a>
			<? }else{ ?>
				<a href="<?=$this->url('/dashboard/form_results/', 'view', '?all=0&sortBy='.$_REQUEST['sortBy'].'&qsid='.$questionSet)?>">Show Paging</a>
			<? } ?>
			
			&nbsp;|&nbsp;
			 
			<? if($_REQUEST['sortBy']=='chrono'){ ?>
				<a href="<?=$this->url('/dashboard/form_results/', 'view', '?all=1&sortBy=newest&qsid='.$questionSet)?>">Sort by Newest</a>
			<? }else{ ?>
				<a href="<?=$this->url('/dashboard/form_results/', 'view', '?all=0&sortBy=chrono&qsid='.$questionSet)?>">Sort Chronologically</a>
			<? } ?>			
			<div class="spacer"></div>
		</div>
	
		<? foreach($answerSets as $answerSetId=>$answerSet){ ?>
			
			<div style="margin:0px; padding:0px; width:100%; height:auto" >
			<table class="entry-form" width="100%" style="margin-bottom:2px">
				<tr>
					<td class="header">Submitted Date</td>
					<td class="header"><?=$answerSet['created']?></td>
				</tr>
				<? 
				$questionNumber=0;
				$numQuestionsToShow=2;
				foreach($questions as $questionId=>$question){ 
					$questionNumber++; 
					?>
					<tr class="<?=($questionNumber>$numQuestionsToShow)?'extra':''?>QuestionRow<?=$answerSetId?> <?=($questionNumber>$numQuestionsToShow)?'noDisplay':'' ?>">
						<td><?=$questions[$questionId]['question']?></td>
						<td><?=$answerSet['answers'][$questionId]['answer']?> <?=$answerSet['answers'][$questionId]['answerLong']?></td>
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