<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<script>
<?php  

$toggleQuestionsShowText = t('View all fields').' &raquo;'; 
$toggleQuestionsHideText = t('Hide fields') . ' &raquo;'; 

?>
var toggleQuestionsShowText='<?php echo $toggleQuestionsShowText?>';
var toggleQuestionsHideText='<?php echo $toggleQuestionsHideText?>';
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
	return confirm("<?php echo t('Are you sure you want to delete this form submission?')?>");
}
//SET UP FORM CONFIRM DELETE
function deleteForm(dLink){
	return confirm("<?php echo t('Are you sure you want to delete this form and its form submissions?')?>");
}
</script> 

<h1><span><?php echo t('Form Results')?></span></h1>

<div class="ccm-dashboard-inner">

<?php  if (count($surveys) == 0) { ?>
<?php echo t('You have not created any forms.')?>
<?php  } else { ?>

<div style="margin:0px; padding:0px; width:100%; height:auto" >

<table class="entry-form" >
	<tr>
		<td class="header"><?php  echo t('Form')?></td>
		<!--our counter insterted-->
		<td class="header"><?php  echo t('Submissions')?></td>
		<td class="header"><?php  echo t('Options')?></td>		
	</tr>
	<?php  
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
				<td><?php  echo $survey['surveyName']?></td>
				<td><?php  echo $survey['answerSetCount']?></td>
				<td>
					<a href="<?php  echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php  echo $c->getCollectionId()?>&qsid=<?php  echo $thisQuestionSetId?>"><?php  echo t('View Responses')?></a>
					|
					<a href="<?php  echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php  echo $ocID?>"><?php  echo t('Open Page')?></a>	
					<?php  if(!intval($blockActiveOnNumPages)){ ?>
					| 
					<a onclick="return deleteForm()" href="<?php  echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php  echo $c->getCollectionId()?>&bID=<?php  echo $survey['bID']?>&qsID=<?php  echo $thisQuestionSetId?>&action=deleteForm"><?php  echo t('Delete Unused Form')?></a>
					<?php  } ?>
				</td>				
			</tr>
		<?php  }
		
	}?>
</table>
</div>

<?php  } ?>

</div>



<?php  if( strlen($questionSet)>0 ){ ?>

	<a name="responses" id="responses"></a>	
	<h1><span><?php echo t('Responses to')?> "<?php echo $surveys[$questionSet]['surveyName']?>"</span></h1>
	<div class="ccm-dashboard-inner">
	
	<?php  if( count($answerSets)==0 ){ ?>
		<div><?php echo t('No one has yet submitted this form.')?></div>
	<?php  }else{ ?>
	
		<div style="margin-bottom:8px">
			<div style="float:right; margin-bottom:8px">
			<a href="<?php echo $this->url('/dashboard/reports/forms/', 'excel', '?qsid=' . $questionSet)?>"><?php echo t('Export to Excel')?> &raquo;</a>
			</div>
			
			<?php  if($_REQUEST['all']!=1){ ?>
				<a href="<?php echo $this->url('/dashboard/reports/forms/', 'view', '?all=1&sortBy='.$_REQUEST['sortBy'].'&qsid='.$questionSet)?>"><?php echo t('Show All')?></a>
			<?php  }else{ ?>
				<a href="<?php echo $this->url('/dashboard/reports/forms/', 'view', '?all=0&sortBy='.$_REQUEST['sortBy'].'&qsid='.$questionSet)?>"><?php echo t('Show Paging')?></a>
			<?php  } ?>
			
			&nbsp;|&nbsp;
			 
			<?php  if($_REQUEST['sortBy']=='chrono'){ ?>
				<a href="<?php echo $this->url('/dashboard/reports/forms/', 'view', '?all=1&sortBy=newest&qsid='.$questionSet)?>"><?php echo t('Sort by Newest')?></a>
			<?php  }else{ ?>
				<a href="<?php echo $this->url('/dashboard/reports/forms/', 'view', '?all=0&sortBy=chrono&qsid='.$questionSet)?>"><?php echo t('Sort Chronologically')?></a>
			<?php  } ?>			
			<div class="spacer"></div>
		</div>
	
		<?php  
		$dh = Loader::helper('date');
		foreach($answerSets as $answerSetId=>$answerSet){ ?>
			
			<div style="margin:0px; padding:0px; width:100%; height:auto" >
			<table class="entry-form" width="100%" style="margin-bottom:2px">
				<tr>
					<td class="header"><?php echo t('Submitted Date')?></td>
					<td class="header"><?php echo $dh->getSystemDateTime($answerSet['created'])?></td>
				</tr>
				<?php  if ($answerSet['uID'] > 0) { ?>
				<tr>
					<td class="subheader"><?php echo t('Submitted By User')?></td>
					<td class="subheader"><?php  
						$ui = UserInfo::getByID($answerSet['uID']);
						if (is_object($ui)) {
							print $ui->getUserName();
						}
						print ' ' . t('(User ID: %s)', $answerSet['uID']);
					} ?></td>
				</tr>				<?php  
				$questionNumber=0;
				$numQuestionsToShow=2;
				foreach($questions as $questionId=>$question){ 
				
					//if this row doesn't have an answer, don't show it.
					if(!strlen(trim($answerSet['answers'][$questionId]['answerLong'])) && 
					   !strlen(trim($answerSet['answers'][$questionId]['answer'])))
					   		continue;
					   
					$questionNumber++; 
					?>
					<tr class="<?php echo ($questionNumber>$numQuestionsToShow)?'extra':''?>QuestionRow<?php echo $answerSetId?> <?php echo ($questionNumber>$numQuestionsToShow)?'noDisplay':'' ?>">
						<td width="33%">
							<?php echo  $questions[$questionId]['question'] ?>
						</td>
						<td>
							<?php 
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
				<?php  } ?>
			</table>
			</div>
			
			<div style="text-align:right; margin-bottom:16px">
			<a onclick="return deleteResponse()" href="<?php  echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php  echo $c->getCollectionId()?>&qsid=<?php  echo $answerSet['questionSetId']?>&asid=<?php  echo $answerSet['asID']?>&action=deleteResponse"><?php echo t("Delete Response")?></a>
			&nbsp;|&nbsp;
			<?php   if( count($questions)>$numQuestionsToShow ){ ?>
				<a onclick="toggleQuestions(<?php  echo $answerSetId?>,this)"><?php  echo $toggleQuestionsShowText?></a>
			<?php   } ?>
			</div>	
		<?php   } ?>
		
	<?php  } ?> 	

	<?php  if($paginator && strlen($paginator->getPages())>0){ ?>	 
		 <div  class="pagination">
			 <div class="pageLeft"><?php echo $paginator->getPrevious()?></div>
			 <div class="pageRight"><?php echo $paginator->getNext()?></div>
			 <?php echo $paginator->getPages()?>
		 </div>		
	<?php  } ?>		
	
	</div>

<?php  } ?>