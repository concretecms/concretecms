<?
Loader::model('userinfo');
$supportHelper=Loader::helper('concrete/support');

if($_POST['new-question-submitted']){
	
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
		
	
		//send data to concrete5.org
		$postResponseData=$supportHelper->postQuestion( $ticketData );
		
		//var_dump($postResponseData);
		
		//check that there were no errors in the remote validation
		$errors=$postResponseData->errors;
		if( !count($errors) ){
			$questionPosted=1;
		}
	}
}

?>


<div id="ccm-supportWrap"> 
	
	<h1 style="margin-top:0px"><?=t('Get Help')?></h1>
	
	<? if($questionPosted){ ?>

		<div style="text-align:center">
			<strong>
				<?=t('Question posted successfully.') ?>
			</strong>
			
			<div style="margin:16px">
				<?=t('You can view your question ')?> 
				<a target="_blank" href="<?=$postResponseData->url ?>"><?=t('here') ?></a>.
			</div>
			
			<a onClick="jQuery.fn.dialog.closeTop()"><?=t('Close Window') ?></a>
		</div>

		
	
	<? }else{ ?>
	
		<h2><?=t('Post Help Request to concrete5.org') ?></h2>
		
		<form id="ccm-support-new-question-form" onSubmit="return ccm_support.submitNewQuestion(this);">
		
			<? if(count($errors)){ ?>
				<div class="alertMsg" style="margin-bottom:16px">
					<strong><?=t('There were some problems with this submission.') ?></strong>
					<? foreach($errors as $error){ ?>
						<div><?=$error ?></div>
					<? } ?>
				</div>		
			<? } ?>
		
			<div>
				<label><?=t('Your Question') ?></label>
				<input name="question" type="text" value="<?=htmlentities($_POST['question'])?>" style="width:98%;" />
			</div>
		
			<div style="margin-top:16px;">				
				<label><?=t('Additional Notes') ?></label>
				<textarea id="ccm-support-question-textarea" name="notes" cols="50" rows="5" style="width:98%;" ><?=htmlentities($_POST['notes'])?></textarea>
			</div>
			
			<div style="margin-top:16px;">	
				<div style="float:left; width:5%">
					<input name="agreed" type="checkbox" value="1" <? if($_POST['agreed']) echo 'checked'; ?> />
				</div>
				<div style="float:left; width:85%">
					<?=t('I understand that information about my website, including its URL, this page, and my concrete5.org account will be shared.') ?>			
					<?=t('No sensitive security information will be disclosed.')?>
				</div>
				<div class="ccm-spacer"></div>
			</div>
			
			<div style="margin-top:16px;">
				<label>concrete5.org Account</label>
				<?=t('You are signed in with the concrete5.org account') ?>
				<a href="<?=CONCRETE5_ORG_URL ?>/profile/-/<?=UserInfo::getRemoteAuthUserId() ?>/" ><?=UserInfo::getRemoteAuthUserName() ?></a>
				<?=t('(Not your account? <a onclick="ccm_support.signOut()">Sign Out</a>)')?>				
			</div>
			
			<div class="bigButtonWrap ccm-buttons">
				<a onclick="$('#ccm-support-new-question-submit').click()" class="ccm-button-right"><span><em class=""><?=t('Ask Question')?></em></span></a>	
				<input type="submit" name="submit" value="submit" style="display: none" id="ccm-support-new-question-submit" />
				<input name="new-question-submitted" type="hidden" value="1">
			</div>
		
		</form> 
	
	<? } ?>

</div>