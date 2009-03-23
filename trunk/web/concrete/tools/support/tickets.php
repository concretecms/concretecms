<?
$supportHelper=Loader::helper('concrete/support');
$questions=$supportHelper->usersTickets();
?>

<div id="ccm-supportWrap"> 

	<h1 style="margin-bottom:0px; padding-bottom:8px;"><?=t('Your Help Requests')?></h1>
	
	<div>
		<label>concrete5.org Account</label>
		<?=t('You are signed in with the concrete5.org account') ?>
		<a href="<?=CONCRETE5_ORG_URL ?>/profile/-/<?=UserInfo::getRemoteAuthUserId() ?>/" ><?=UserInfo::getRemoteAuthUserName() ?></a>
		<?=t('(Not your account? <a onclick="ccm_support.signOut()">Sign Out</a>)')?>				
	</div>

	<? if( !count($questions) ){ ?>
	
		<div><?=t('You have not submitted any tickets.')?></div>		
	
	<? }else{ ?>
	
		<table>
			<tr>
				<td><?=t('Question')?></td>
				<td></td>			
			</tr>
			
			<? foreach($questions as $question){ ?>
			<tr>
				<td><? var_dump($question) ?></td>
				<td></td>
			</tr>
			<? } ?>
		
		</table>

	<? } ?>
</div>