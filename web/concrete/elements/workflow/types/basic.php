<fieldset>
<legend><?=t('Approval & Notification')?></legend>

<div class="clearfix">
<div class="input">

<?=Loader::element('workflow/access_list')?>

</fieldset>

<script type="text/javascript">

ccm_addAccessEntity = function(peID, pdID) {
	jQuery.fn.dialog.closeTop();
	jQuery.fn.dialog.showLoader();
	
	$.get('<?=$workflow->getWorkflowToolsURL("add_access_entity")?>&pdID=' + pdID + '&peID=' + peID, function() { 
		alert("ok");
	});
}

ccm_deleteAccessEntityAssignment = function(peID) {
	jQuery.fn.dialog.showLoader();
	
	$.get('<?=$workflow->getWorkflowToolsURL("remove_access_entity")?>&peID=' + peID, function() { 
		alert("ok");
	});
}


</script>