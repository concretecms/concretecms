<?
 
$searchInstance = $_REQUEST['searchInstance'];

?>

<div style="text-align:center">

	<div style="margin:24px 0px; font-weight:bold">
		<?=count($_REQUEST['fID'])?> file<?=(count($_REQUEST['fID'])!=1)?'s':''?> uploaded successfully.
	</div>
	
	<div style="margin-bottom:24px">
		<a onClick="ccm_filesApplySetsToUploaded([<?=join(',',$_REQUEST['fID'])?>], '<?=$searchInstance?>');">Assign File Sets</a> 
			&nbsp;|&nbsp; 
		<a onClick="ccm_filesApplyPropertiesToUploaded([<?=join(',',$_REQUEST['fID'])?>], '<?=$searchInstance?>');">Edit Properties</a>
	</div>
	
	[ <a onClick="jQuery.fn.dialog.closeTop()"><?=t('Close Window') ?></a> ]

</div>