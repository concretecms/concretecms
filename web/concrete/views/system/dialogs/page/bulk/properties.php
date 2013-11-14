<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div id="ccm-page-properties" class="ccm-ui">

<table border="0" cellspacing="0" cellpadding="0" class="ccm-grid">
<?
foreach($attribs as $at) {
	$controller->printAttributeRow($at);
}
?>
</table>

<br/>  

</div>

<script type="text/javascript">
$(function() { 
	//ccm_activateEditablePropertiesGrid();  
});
</script>