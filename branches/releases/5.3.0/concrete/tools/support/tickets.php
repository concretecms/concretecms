<?php 
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

	<h1 style="margin-bottom:0px; padding-bottom:8px;"><?php echo t('Your Help Requests')?></h1>
	
	<div>
		<label>concrete5.org Account</label>
		<?php echo t('You are signed in with the concrete5.org account') ?>
		<a href="<?php echo CONCRETE5_ORG_URL ?>/profile/-/<?php echo UserInfo::getRemoteAuthUserId() ?>/" ><?php echo UserInfo::getRemoteAuthUserName() ?></a>
		<?php echo t('(Not your account? <a onclick="ccm_support.signOut(jQuery.fn.dialog.closeTop)">Sign Out</a>)')?>				
	</div>

	<?php  if( !is_array($responseData->tickets) || !count($responseData->tickets) ){ ?>
	
		<div style="margin:32px 0px; font-weight:bold"><?php echo t('You have not submitted any tickets.')?></div>		
	
	<?php  }else{ ?>
	
		<div style="margin-top:16px"><?php echo t('Viewing your last %s help requests:', count($responseData->tickets))?></div>
	
		<table class="ccm-dataGrid" style="width:100%; margin-top:16px">
			<tr class="header" >
				<td><?php echo t('Question')?></td>
				<td style="width:50px"><?php echo t('Replies')?></td>	
				<td style="width:50px"><?php echo t('Status')?></td>	
				<td style="width:100px"><?php echo t('Submitted')?></td>						
			</tr>
			
			<?php  foreach($responseData->tickets as $ticket){ ?>
			<tr>
				<td>
					<div><a href="<?php echo $ticket->url ?>" target="_blank"><?php echo  $ticket->title ?></a> 
					<?php  
					//make as new if the ticket was repied to since last time the user viewed this page, or within 5 minutes
					if( $lastSupportListViewTime < ($ticket->lastReplyTime+300) ){ ?>
					<strong><?php echo t('*NEW REPLY*')?></strong>
					<?php  } ?>
					</div>
				</td>
				<td><?php echo  $ticket->replies ?></td>
				<td><?php echo  $ticket->status ?></td>
				<td><?php echo date("M d, Y", strtotime($ticket->date))?></td>				
			</tr>
			<?php  } ?>
		
		</table>

	<?php  } ?>
	
	
	<div style="margin-top:16px"><a onclick="ccm_support.show();"><?php echo t('&laquo; Search the Knowledge Base') ?></a></div>
</div>
