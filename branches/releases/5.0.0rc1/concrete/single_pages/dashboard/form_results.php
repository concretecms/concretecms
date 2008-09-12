<script>
<?php  $toggleQuestionsShowText='View all fields &raquo;' ?>
var toggleQuestionsShowText='<?php echo $toggleQuestionsShowText?>';
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

<?php  if (count($surveys) == 0) { ?>
You have not created any forms.
<?php  } else { ?>

<div style="margin:0px; padding:0px; width:100%; height:auto" >
<table class="entry-form" >
	<tr>
		<td class="header">Survey</td>
		<td class="header">Options</td>
	</tr>
	<?php  foreach($surveys as $thisQuestionSetId=>$survey){
		$b=Block::getByID( intval($survey['bID']) );
		if (is_object($b)) {
			$oc = $b->getBlockCollectionObject();
			$ocID = $oc->getCollectionID();		
			?>
			<tr>
				<td><?php echo $survey['surveyName']?></td>
				<td>
					<a href="<?php echo DIR_REL?>/index.php?cID=<?php echo $c->getCollectionId()?>&qsid=<?php echo $thisQuestionSetId?>">View Responses</a>
					|
					<a href="<?php echo DIR_REL?>/index.php?cID=<?php echo $ocID?>">Open Page</a>	
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
	<h1><span>Responses to "<?php echo $surveys[$questionSet]['surveyName']?>"</span></h1>
	<div class="ccm-dashboard-inner">
	
	<?php  if( count($answerSets)==0 ){ ?>
		<div>No one has yet submitted this form.</div>
	<?php  }else{ ?>
	
		<div style="margin-bottom:8px">
			<div style="float:right; margin-bottom:8px">
			<a href="<?php echo $this->url('/dashboard/form_results/', 'excel', '?qsid=' . $questionSet)?>">Export to Excel &raquo;</a>
			</div>
			
			<?php  if($_REQUEST['all']!=1){ ?>
				<a href="<?php echo $this->url('/dashboard/form_results/', 'view', '?all=1&sortBy='.$_REQUEST['sortBy'].'&qsid='.$questionSet)?>">Show All</a>
			<?php  }else{ ?>
				<a href="<?php echo $this->url('/dashboard/form_results/', 'view', '?all=0&sortBy='.$_REQUEST['sortBy'].'&qsid='.$questionSet)?>">Show Paging</a>
			<?php  } ?>
			
			&nbsp;|&nbsp;
			 
			<?php  if($_REQUEST['sortBy']=='chrono'){ ?>
				<a href="<?php echo $this->url('/dashboard/form_results/', 'view', '?all=1&sortBy=newest&qsid='.$questionSet)?>">Sort by Newest</a>
			<?php  }else{ ?>
				<a href="<?php echo $this->url('/dashboard/form_results/', 'view', '?all=0&sortBy=chrono&qsid='.$questionSet)?>">Sort Chronologically</a>
			<?php  } ?>			
			<div class="spacer"></div>
		</div>
	
		<?php  foreach($answerSets as $answerSetId=>$answerSet){ ?>
			
			<div style="margin:0px; padding:0px; width:100%; height:auto" >
			<table class="entry-form" width="100%" style="margin-bottom:2px">
				<tr>
					<td class="header">Submitted Date</td>
					<td class="header"><?php echo $answerSet['created']?></td>
				</tr>
				<?php  
				$questionNumber=0;
				$numQuestionsToShow=2;
				foreach($questions as $questionId=>$question){ 
					$questionNumber++; 
					?>
					<tr class="<?php echo ($questionNumber>$numQuestionsToShow)?'extra':''?>QuestionRow<?php echo $answerSetId?> <?php echo ($questionNumber>$numQuestionsToShow)?'noDisplay':'' ?>">
						<td><?php echo $questions[$questionId]['question']?></td>
						<td><?php echo $answerSet['answers'][$questionId]['answer']?> <?php echo $answerSet['answers'][$questionId]['answerLong']?></td>
					</tr>
				<?php  } ?>
			</table>
			</div>
			
			<?php  if( count($questions)>$numQuestionsToShow ){ ?>
				<div style="text-align:right; margin-bottom:16px"><a onclick="toggleQuestions(<?php echo $answerSetId?>,this)"><?php echo $toggleQuestionsShowText?></a></div>
			<?php  } ?>
			
		<?php  } ?>
		
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