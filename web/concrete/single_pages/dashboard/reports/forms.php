<?php defined('C5_EXECUTE') or die("Access Denied.");
/* @var $h ConcreteDashboardHelper */
$h = Loader::helper('concrete/dashboard');
/* @var $ih ConcreteInterfaceHelper */
$ih = Loader::helper('concrete/interface');
/* @var $nh NavigationHelper */
$nh = Loader::helper('navigation');
/* @var $text TextHelper */
$text = Loader::helper('text');
/* @var $db DataBase */
$db = Loader::db();
?>
<?=$h->getDashboardPaneHeaderWrapper(t('Form Results'), false, false, false);?>
<div class="ccm-pane-body">
<table class="zebra-striped">
	<thead>
		<tr>
			<th><?php echo t('Form')?></th>
			<th><?php echo t('Submissions')?></th>
			<th><?php echo t('Options')?></th>
		</tr>
	</thead>
	<tbody>
		<?foreach ($surveys as $qsid => $survey):
		$block = Block::getByID((int) $survey['bID']);
		if (!is_object($block)) {
			continue;
		}
		$pages = $db->getOne(
			'SELECT count(*)
			FROM CollectionVersionBlocks
			INNER JOIN Pages
			ON CollectionVersionBlocks.cID = Pages.cID
			INNER JOIN CollectionVersions
			ON CollectionVersions.cID = Pages.cID
			WHERE CollectionVersions.cvIsApproved = 1
			AND CollectionVersionBlocks.cvID = CollectionVersions.cvID
			AND CollectionVersionBlocks.bID = ?',
			array($block->bID)
		);
		$url = $nh->getLinkToCollection($block->getBlockCollectionObject());
?>
		<tr>
			<td><?=$text->entities($survey['surveyName'])?></td>
			<td><?=$text->entities($survey['answerSetCount'])?></td>
			<td>
				<a href="<?=$this->action()?>"></a>
			</td>
		</tr>
		<?endforeach?>
	<? 
	$db = Loader::db();
	foreach($surveys as $thisQuestionSetId=>$survey){
		$b=Block::getByID( intval($survey['bID']) );
		
		//get count of number of times this block is used
		$db = Loader::db();
		$q = "select count(*) from CollectionVersionBlocks inner join Pages on (CollectionVersionBlocks.cID = Pages.cID) inner join CollectionVersions on (CollectionVersions.cID = Pages.cID) where CollectionVersions.cvIsApproved=1 AND CollectionVersionBlocks.cvID=CollectionVersions.cvID AND CollectionVersionBlocks.bID = '{$b->bID}'";
		$blockActiveOnNumPages = $db->getOne($q);
		
		if (is_object($b)) {
			$oc = $b->getBlockCollectionObject();
			$ocID = $oc->getCollectionID();		
			?>
			<tr>
				<td><?php echo $survey['surveyName']?></td>
				<td><?php echo $survey['answerSetCount']?></td>
				<td>
					<a href="<?php echo DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?php echo $c->getCollectionId()?>&qsid=<?php echo $thisQuestionSetId?>"><?php echo t('View Responses')?></a>
					|
					<a href="<?php echo DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?php echo $ocID?>"><?php echo t('Open Page')?></a>	
					<? if(!intval($blockActiveOnNumPages)){ ?>
					| 
					<a onclick="return deleteForm()" href="<?php echo DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?php echo $c->getCollectionId()?>&bID=<?php echo $survey['bID']?>&qsID=<?php echo $thisQuestionSetId?>&action=deleteForm"><?php echo t('Delete Unused Form')?></a>
					<? } ?>
				</td>				
			</tr>
		<? }
		
	}?>
	</tbody>
</table>
</div><div class="ccm-pane-footer">

</div>
<?=$h->getDashboardPaneFooterWrapper(false);?>
<?return;?>
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
//SET UP FORM RESPONSE CONFIRM DELETE
function deleteResponse(dLink){
	return confirm("<?=t('Are you sure you want to delete this form submission?')?>");
}
//SET UP FORM CONFIRM DELETE
function deleteForm(dLink){
	return confirm("<?=t('Are you sure you want to delete this form and its form submissions?')?>");
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
		<td class="header"><?php echo t('Form')?></td>
		<!--our counter insterted-->
		<td class="header"><?php echo t('Submissions')?></td>
		<td class="header"><?php echo t('Options')?></td>		
	</tr>
	<? 
	$db = Loader::db();
	foreach($surveys as $thisQuestionSetId=>$survey){
		$b=Block::getByID( intval($survey['bID']) );
		
		//get count of number of times this block is used
		$db = Loader::db();
		$q = "select count(*) from CollectionVersionBlocks inner join Pages on (CollectionVersionBlocks.cID = Pages.cID) inner join CollectionVersions on (CollectionVersions.cID = Pages.cID) where CollectionVersions.cvIsApproved=1 AND CollectionVersionBlocks.cvID=CollectionVersions.cvID AND CollectionVersionBlocks.bID = '{$b->bID}'";
		$blockActiveOnNumPages = $db->getOne($q);
		
		if (is_object($b)) {
			$oc = $b->getBlockCollectionObject();
			$ocID = $oc->getCollectionID();		
			?>
			<tr>
				<td><?php echo $survey['surveyName']?></td>
				<td><?php echo $survey['answerSetCount']?></td>
				<td>
					<a href="<?php echo DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?php echo $c->getCollectionId()?>&qsid=<?php echo $thisQuestionSetId?>"><?php echo t('View Responses')?></a>
					|
					<a href="<?php echo DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?php echo $ocID?>"><?php echo t('Open Page')?></a>	
					<? if(!intval($blockActiveOnNumPages)){ ?>
					| 
					<a onclick="return deleteForm()" href="<?php echo DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?php echo $c->getCollectionId()?>&bID=<?php echo $survey['bID']?>&qsID=<?php echo $thisQuestionSetId?>&action=deleteForm"><?php echo t('Delete Unused Form')?></a>
					<? } ?>
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
	
		<? 
		$dh = Loader::helper('date');
		foreach($answerSets as $answerSetId=>$answerSet){ ?>
			
			<div style="margin:0px; padding:0px; width:100%; height:auto" >
			<table class="entry-form" width="100%" style="margin-bottom:2px">
				<tr>
					<td class="header"><?=t('Submitted Date')?></td>
					<td class="header"><?=$dh->getSystemDateTime($answerSet['created'])?></td>
				</tr>
				<? if ($answerSet['uID'] > 0) { ?>
				<tr>
					<td class="subheader"><?=t('Submitted By User')?></td>
					<td class="subheader"><? 
						$ui = UserInfo::getByID($answerSet['uID']);
						if (is_object($ui)) {
							print $ui->getUserName();
						}
						print ' ' . t('(User ID: %s)', $answerSet['uID']);
					} ?></td>
				</tr>				<? 
				$questionNumber=0;
				$numQuestionsToShow=2;
				foreach($questions as $questionId=>$question){ 
				
					//if this row doesn't have an answer, don't show it.
					if(!strlen(trim($answerSet['answers'][$questionId]['answerLong'])) && 
					   !strlen(trim($answerSet['answers'][$questionId]['answer'])))
					   		continue;
					   
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
									echo '<a href="' . $fileVersion->getRelativePath() .'">'.$fileVersion->getFileName().'</a>';
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
			
			<div style="text-align:right; margin-bottom:16px">
			<a onclick="return deleteResponse()" href="<?php echo DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?php echo $c->getCollectionId()?>&qsid=<?php echo $answerSet['questionSetId']?>&asid=<?php echo $answerSet['asID']?>&action=deleteResponse"><?=t("Delete Response")?></a>
			&nbsp;|&nbsp;
			<?php  if( count($questions)>$numQuestionsToShow ){ ?>
				<a onclick="toggleQuestions(<?php echo $answerSetId?>,this)"><?php echo $toggleQuestionsShowText?></a>
			<?php  } ?>
			</div>	
		<?php  } ?>
		
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