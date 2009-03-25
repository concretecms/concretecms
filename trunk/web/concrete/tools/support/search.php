<?
$supportHelper=Loader::helper('concrete/support');
?>

<div id="ccm-supportWrap">

	<input id="ccm-isRemotelyLogged" name="isRemotelyLogged" type="hidden" value="<?=(UserInfo::isRemotelyLoggedIn())?1:0; ?>" />

	<a href="<?=MENU_HELP_URL?>" target="_blank" style="float:right; padding-top:8px"><?=t('Browse Full Documentation')?> &raquo;</a>
	
	<h1 style="margin-top:0px"><?=t('Get Help')?></h1>
	
	<? if($_POST['ccm-support-question-submitted']){ ?>

		<?
		$answers=$supportHelper->askQuestion( $_POST['question'] );
		?>
		
		<h2><?=count($answers)?> <?=t('responses for "') ?><?=$_POST['question']?><?=t('"')?></h2>
		
		<? if( !is_array($answers) ){ ?>
		
			<div class="alertMsg">
			
				<?=t('Error: Could not connect to concrete5.org')?>
						
			</div>
		
		<? }elseif( count($answers) ){ ?>		
			<div id="answers">
				<?
				$answerNum=0;
				foreach($answers as $answer){ 
					$answerNum++;?>
					<a href="<?=$answer->full_url ?>" target="_blank">
					<div class="answer">
						<div class="title"><?=$answer->cName ?> </div>
						<div class="description">
							<?=$answer->cDescription ?>
						</div>
					</div>
					</a>
				<? } ?>
			</div>
			
			<div class="bigButtonWrap ccm-buttons" >
				<a onclick="ccm_support.showQuestionForm()" class="ccm-button"><span><em class=""><?=t('None of these answer my question')?></em></span></a>	
				<div class="ccm-throbber" style="left:-40px; position:relative; top:0px;"></div>
			</div>	
						
		<? }else{ ?>	
	
			<div class="bigButtonWrap ccm-buttons" >
				<a onclick="ccm_support.showQuestionForm()" class="ccm-button"><span><em class=""><?=t('Post Help Request to concrete5.org')?></em></span></a>	
				<div class="ccm-throbber" style="left:-40px; position:relative; top:0px;"></div>
			</div>	
			
		<? } ?>
	
	<? }else{ ?>
	
		<h2><?=t('What do you need help with?') ?></h2>
		
		<form id="ccm-support-question-form" onSubmit="return ccm_support.searchAnswers(this);">
		
			<div style="margin-top:16px; text-align:center"> 
				<input id="ccm-support-question-textarea" name="question" type="text" value="" style="width:98%; margin:auto; font-size:18px; padding:4px" />
			</div>
			
			<div class="ccm-buttons">
				<a onclick="$('#ccm-support-question-submit').click()" class="ccm-button-right"><span><em class=""><?=t('Search')?></em></span></a>	
				<input type="submit" name="submit" value="submit" style="display: none" id="ccm-support-question-submit" />
				<input name="ccm-support-question-submitted" type="hidden" value="1">
				<div class="ccm-throbber" style="float:right; margin-top:10px; display:none"></div>
			</div>
		
		</form>
		
		<div class="ccm-spacer"></div>
		
		<? if(UserInfo::getRemoteAuthInSupportGroup()){ ?>
			<a onclick="ccm_support.showMyTickets()"><?=t('View Old Help Requests')?> &raquo;</a>
		<? }elseif(!UserInfo::isRemotelyLoggedIn()){ ?>
			<a onclick="ccm_support.showMyTickets()"><?=t('Login to view your old help requests') ?></a>
		<? } ?>
	
	<? } ?>

</div>