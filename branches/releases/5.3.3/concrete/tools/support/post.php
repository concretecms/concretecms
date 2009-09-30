<?php 
Loader::model('userinfo');
$supportHelper=Loader::helper('concrete/support');

if($_POST['new-question-submitted'] && UserInfo::getRemoteAuthInSupportGroup()){
	
	$errors=array();
	 
	if( !UserInfo::isRemotelyLoggedIn() ){
		$errors[]=t('You must log back in to post a new question');
	}		
	
	if(!trim($_POST['question'])){
		$errors[]=t('You must include a question');
	}		
	
	if(!$_POST['agreed']){
		$errors[]=t('You must check the box stating that you understand how your question will be used.');
	}
	
	if(!count($errors)){
	
		$ticketData=array();
		$ticketData['question']=$_POST['question'];
		$ticketData['notes']=$_POST['notes'];
		
		//diagnositic data
		$diagnosticData=$supportHelper->getDiagnosticData();
		$ticketData=array_merge($ticketData,$diagnosticData);
	
		//send data to concrete5.org
		$postResponseData=$supportHelper->postQuestion( $ticketData );
		
		//var_dump($postResponseData);
		
		//check that there were no errors in the remote validation
		$errors=$postResponseData->errors;
		if( !count($errors) &&  $postResponseData->success){
			$questionPosted=1;
		}
	}
}

?>


<div id="ccm-supportWrap"> 
	
	<h1 style="margin-top:0px"><?php echo t('Get Help')?></h1>
	
	<?php  if( !UserInfo::getRemoteAuthInSupportGroup() ){ ?>
	
		<div style="">
		
			<div style="margin-bottom:16px"><?php echo t('Help is unavailable, but you can get help if you need it')?></div>
		
			<div class="bigButtonWrap ccm-buttons">
				<a href="<?php echo KNOWLEDGE_BASE_SUPPORT_LEARN_MORE_URL?>" target="_blank" onclick="" class="ccm-button-right"><span><em class=""><?php echo t('Learn More')?></em></span></a>
			</div>
			
			<div class="ccm-spacer"></div>
			
			<?php  if( UserInfo::isRemotelyLoggedIn() ){ ?>
			<div style="margin-top:16px;">
				<label>concrete5.org Account</label>
				<?php echo t('You are signed in with the concrete5.org account') ?>
				<a href="<?php echo CONCRETE5_ORG_URL ?>/profile/-/<?php echo UserInfo::getRemoteAuthUserId() ?>/" ><?php echo UserInfo::getRemoteAuthUserName() ?></a>
				<?php echo t('(Not your account? <a onclick="ccm_support.signOut(jQuery.fn.dialog.closeTop)">Sign Out</a>)')?>				
			</div>		
			<?php  } ?>	
		
		</div>
	
	
	<?php  }elseif($questionPosted){ ?>

		<div style="text-align:center">
			<strong>
				<?php echo t('Question posted successfully.') ?>
			</strong>
			
			<div style="margin:16px">
				<?php echo t('You can view your question ')?> 
				<a target="_blank" href="<?php echo $postResponseData->url ?>"><?php echo t('here') ?></a>.
			</div>
			
			<a onClick="jQuery.fn.dialog.closeTop()"><?php echo t('Close Window') ?></a>
		</div>

		
	
	<?php  }else{ ?>
	
		<h2><?php echo t('Post Help Request to concrete5.org') ?></h2>
		
		<form id="ccm-support-new-question-form" onSubmit="return ccm_support.submitNewQuestion(this);">
		
			<input name="pg_url" type="hidden" value="<?php echo htmlentities($_REQUEST['pg_url']) ?>" />
		
			<?php  if(count($errors)){ ?>
				<div class="alertMsg" style="margin-bottom:16px">
					<strong><?php echo t('There were some problems with this submission.') ?></strong>
					<?php  foreach($errors as $error){ ?>
						<div><?php echo $error ?></div>
					<?php  } ?>
				</div>		
			<?php  } ?>
		
			<div>
				<label><?php echo t('Your Question') ?></label>
				<input name="question" type="text" value="<?php echo htmlentities($_POST['question'])?>" style="width:98%;" />
			</div>
		
			<div style="margin-top:16px;">				
				<label><?php echo t('Additional Notes') ?></label>
				<textarea id="ccm-support-question-textarea" name="notes" cols="50" rows="5" style="width:98%;" ><?php echo htmlentities($_POST['notes'])?></textarea>
			</div>
			
			<div style="margin-top:16px;">	
				<div style="float:left; width:5%">
					<input name="agreed" type="checkbox" value="1" <?php  if($_POST['agreed']) echo 'checked'; ?> />
				</div>
				<div style="float:left; width:85%">
					<?php echo t('I understand that information about my website, including its URL, this page, and my concrete5.org account will be shared.') ?>			
					<?php echo t('No sensitive security information will be disclosed.')?>
				</div>
				<div class="ccm-spacer"></div>
			</div>
			
			<div style="margin:8px 0px 16px 0px;">
				<label>concrete5.org Account</label>
				<?php echo t('You are signed in with the concrete5.org account') ?>
				<a href="<?php echo CONCRETE5_ORG_URL ?>/profile/-/<?php echo UserInfo::getRemoteAuthUserId() ?>/" ><?php echo UserInfo::getRemoteAuthUserName() ?></a>
				<?php echo t('(Not your account? <a onclick="ccm_support.signOut(jQuery.fn.dialog.closeTop)">Sign Out</a>)')?>				
			</div>
			
			<div class="bigButtonWrap ccm-buttons">
				<a onclick="$('#ccm-support-new-question-submit').click()" class="ccm-button-right"><span><em class=""><?php echo t('Ask Question')?></em></span></a>	
				<input type="submit" name="submit" value="submit" style="display: none" id="ccm-support-new-question-submit" />
				<input name="new-question-submitted" type="hidden" value="1">
			</div>
		
		</form> 
	
	<?php  } ?>

</div>
