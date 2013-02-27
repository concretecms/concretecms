<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
$ctArray = CollectionType::getList();
$types = array();
foreach($ctArray as $ct) {
	$types[$ct->getCollectionTypeID()] = $ct->getCollectionTypeName();
}

if (is_object($configuration)) { 
	$ctIDs = $configuration->getCollectionTypeIDs();
}

?>
<fieldset>
	<div class="control-group">
		<label class="control-label"><?=t('Limit By Page Type')?></label>
		<div class="controls">
			<?=$form->selectMultiple('ctIDs', $types, $ctIDs)?>
		</div>
	</div>
</fieldset>


<script type="text/javascript">
$(function() {
	$('#ctIDs').chosen();
});
</script>