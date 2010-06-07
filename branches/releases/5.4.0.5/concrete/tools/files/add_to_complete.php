<?php 
 
$searchInstance = $_REQUEST['searchInstance'];

?>

<div style="text-align:center">

	<div style="margin:24px 0px; font-weight:bold">
		<?php  if (count($_REQUEST['fID']) == 1) { ?>
			<?php echo t('1 file uploaded successfully.')?>
		<?php  } else { ?>
			<?php echo t('%s files uploaded successfully.', count($_REQUEST['fID']))?>
		<?php  } ?>
	</div>
	
	<div style="margin-bottom:24px">
		<a onClick="ccm_filesApplySetsToUploaded([<?php echo join(',',$_REQUEST['fID'])?>], '<?php echo $searchInstance?>');"><?php echo t('Assign File Sets')?></a> 
			&nbsp;|&nbsp; 
		<a onClick="ccm_filesApplyPropertiesToUploaded([<?php echo join(',',$_REQUEST['fID'])?>], '<?php echo $searchInstance?>');"><?php echo t('Edit Properties')?></a>
	</div>
	
	[ <a onClick="jQuery.fn.dialog.closeTop()"><?php echo t('Close Window') ?></a> ]

</div>