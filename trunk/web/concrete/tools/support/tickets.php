<?
$supportHelper=Loader::helper('concrete/support');
$responseData=$supportHelper->usersTickets();

$_SESSION['newHelpResponseWaiting']=0;
$lastSupportListViewTime=$_SESSION['lastSupportListViewTime'];
//echo $_SESSION['newHelpResponseWaiting'].'<br>';
$_SESSION['lastSupportListViewTime']=time();
if(!$lastSupportListViewTime) $lastSupportListViewTime=time();  

//var_dump($responseData);
?>

<div id="ccm-supportWrap"> 

	<h1 style="margin-bottom:0px; padding-bottom:8px;"><?=t('Your Help Requests')?></h1>
	
	<div>
		<label>concrete5.org Account</label>
		<?=t('You are signed in with the concrete5.org account') ?>
		<a href="<?=CONCRETE5_ORG_URL ?>/profile/-/<?=UserInfo::getRemoteAuthUserId() ?>/" ><?=UserInfo::getRemoteAuthUserName() ?></a>
		<?=t('(Not your account? <a onclick="ccm_support.signOut(jQuery.fn.dialog.closeTop)">Sign Out</a>)')?>				
	</div>

	<? if( !is_array($responseData->tickets) || !count($responseData->tickets) ){ ?>
	
		<div style="margin:32px 0px; font-weight:bold"><?=t('You have not submitted any tickets.')?></div>		
	
	<? }else{ ?>
	
		<div style="margin-top:16px"><?=t('Viewing your last %s help requests:', count($responseData->tickets))?></div>
	
		<table class="ccm-dataGrid" style="width:100%; margin-top:16px">
			<tr class="header" >
				<td><?=t('Question')?></td>
				<td style="width:50px"><?=t('Replies')?></td>	
				<td style="width:100px"><?=t('Submitted')?></td>						
			</tr>
			
			<? foreach($responseData->tickets as $ticket){ ?>
			<tr>
				<td>
					<div><a href="<?=$ticket->url ?>" target="_blank"><?= $ticket->title ?></a> 
					<? 
					//make as new if the ticket was repied to since last time the user viewed this page, or within 5 minutes
					if( $lastSupportListViewTime < ($ticket->lastReplyTime+300) ){ ?>
					<strong><?=t('*NEW REPLY*')?></strong>
					<? } ?>
					</div>
				</td>
				<td><?= $ticket->replies ?></td>
				<td><?=date("M d, Y", strtotime($ticket->date))?></td>				
			</tr>
			<? } ?>
		
		</table>

	<? } ?>
	
	
	<div style="margin-top:16px"><a onclick="ccm_support.show();"><?=t('&laquo; Search the Knowledge Base') ?></a></div>
</div>
