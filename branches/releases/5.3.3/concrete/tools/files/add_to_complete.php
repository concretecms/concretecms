<?php 
 

?>

<div style="text-align:center">

	<div style="margin:24px 0px; font-weight:bold">
		<?php echo count($_REQUEST['fID'])?> file<?php echo (count($_REQUEST['fID'])!=1)?'s':''?> uploaded successfully.
	</div>
	
	<div style="margin-bottom:24px">
		<a onClick="ccm_filesApplySetsToUploaded([<?php echo join(',',$_REQUEST['fID'])?>]);">Assign File Sets</a> 
			&nbsp;|&nbsp; 
		<a onClick="ccm_filesApplyPropertiesToUploaded([<?php echo join(',',$_REQUEST['fID'])?>]);">Edit Properties</a>
	</div>
	
	[ <a onClick="jQuery.fn.dialog.closeTop()"><?php echo t('Close Window') ?></a> ]

</div>