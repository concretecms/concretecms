<?
 
$searchInstance = $_REQUEST['searchInstance'];

?>

<div style="text-align:center">

	<div style="margin:24px 0px; font-weight:bold">
		<? if (count($_REQUEST['fID']) == 1) { ?>
			<?=t('1 file uploaded successfully.')?>
		<? } else { ?>
			<?=t('%s files uploaded successfully.', count($_REQUEST['fID']))?>
		<? } ?>
	</div>
	
	<div style="margin-bottom:24px">
		<a onClick="ccm_filesApplySetsToUploaded([<?=join(',',$_REQUEST['fID'])?>], '<?=$searchInstance?>');"><?=t('Assign File Sets')?></a> 
			&nbsp;|&nbsp; 
		<a onClick="ccm_filesApplyPropertiesToUploaded([<?=join(',',$_REQUEST['fID'])?>], '<?=$searchInstance?>');"><?=t('Edit Properties')?></a>
	</div>
	
	[ <a onClick="jQuery.fn.dialog.closeTop()"><?=t('Close Window') ?></a> ]

</div>