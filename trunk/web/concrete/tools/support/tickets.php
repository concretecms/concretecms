<?
$supportHelper=Loader::helper('concrete/support');
$questions=$supportHelper->usersTickets();
?>

<div id="ccm-supportWrap"> 

	<h1 style=""><?=t('Your Help Requests')?></h1>

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