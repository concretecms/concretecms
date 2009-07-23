<?php 
$supportHelper=Loader::helper('concrete/support');
?>

<div id="ccm-supportWrap">

	<input id="ccm-isRemotelyLogged" name="isRemotelyLogged" type="hidden" value="<?php echo (UserInfo::isRemotelyLoggedIn())?1:0; ?>" />

	<a href="<?php echo MENU_HELP_URL?>" target="_blank" style="float:right; padding-top:8px"><?php echo t('Browse Full Documentation')?> &raquo;</a>
	
	<h1 style="margin-top:0px"><?php echo t('Get Help')?></h1>
	
	<?php  if($_POST['ccm-support-question-submitted']){ ?>

		<?php 
		$answers=$supportHelper->askQuestion( $_POST['question'] );
		?>
		
		<h2><?php  printf(ngettext('%d result found.', '%d results found.', count($answers)), count($answers))?> <a href="javascript:void(0)" onclick="ccm_support.show()"><?php echo t('Search again.')?></a></h2>
		
		<?php  if( !is_array($answers) ){ ?>
		
			<div class="alertMsg">
			
				<?php echo t('Error: Could not connect to concrete5.org')?>
						
			</div>
		
		<?php  }elseif( count($answers) ){ ?>		
			<div id="answers">
				<?php 
				$answerNum=0;
				foreach($answers as $answer){ 
					$answerNum++;?>
					<a href="<?php echo $answer->full_url ?>" target="_blank">
					<div class="answer">
						<div class="title"><?php echo $answer->cName ?> </div>
						<div class="description">
							<?php echo $answer->cDescription ?>
						</div>
					</div>
					</a>
				<?php  } ?>
			</div>
			
			<div class="bigButtonWrap ccm-buttons" >
				<a onclick="ccm_support.showQuestionForm()" class="ccm-button"><span><em class=""><?php echo t('None of these answer my question')?></em></span></a>	
				<div class="ccm-throbber" style="left:-40px; position:relative; top:0px;"></div>
			</div>	
						
		<?php  }else{ ?>	
	
			<div class="bigButtonWrap ccm-buttons" >
				<a onclick="ccm_support.showQuestionForm()" class="ccm-button"><span><em class=""><?php echo t('Post Help Request to concrete5.org')?></em></span></a>	
				<div class="ccm-throbber" style="left:-40px; position:relative; top:0px;"></div>
			</div>	
			
		<?php  } ?>
	
	<?php  }else{ ?>
	
		<h2><?php echo t('What do you need help with?') ?></h2>
		
		<form id="ccm-support-question-form" onSubmit="return ccm_support.searchAnswers(this);">
		
			<div style="margin-top:16px; text-align:center"> 
				<input id="ccm-support-question-textarea" name="question" type="text" value="" style="width:98%; margin:auto; font-size:18px; padding:4px" />
			</div>
			
			<div class="ccm-buttons">
				<a onclick="$('#ccm-support-question-submit').click()" class="ccm-button-right"><span><em class=""><?php echo t('Search')?></em></span></a>	
				<input type="submit" name="submit" value="submit" style="display: none" id="ccm-support-question-submit" />
				<input name="ccm-support-question-submitted" type="hidden" value="1">
				<div class="ccm-throbber" style="float:right; margin-top:10px; display:none"></div>
			</div>
		
		</form>
		
		<div class="ccm-spacer"></div>
		
		<?php  if(UserInfo::getRemoteAuthInSupportGroup()){ ?>
			<a onclick="ccm_support.showMyTickets()"><?php echo t('View Old Help Requests')?> &raquo;</a>
		<?php  }elseif(!UserInfo::isRemotelyLoggedIn()){ ?>
			<a onclick="ccm_support.showMyTickets()"><?php echo t('Login to view your old help requests') ?></a>
		<?php  } ?>
	
	<?php  } ?>

</div>