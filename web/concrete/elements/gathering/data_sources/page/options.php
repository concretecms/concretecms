<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
$ctArray = CollectionType::getList();
$types = array();
foreach($ctArray as $ct) {
	$types[$ct->getCollectionTypeID()] = $ct->getCollectionTypeName();
}

if (is_object($configuration)) { 
	$ctID = $configuration->getCollectionTypeID();
}

?>
<div class="control-group">
	<label class="control-label"><?=t('Limit By Page Type')?></label>
	<div class="controls" data-select="page">
		<?=$form->select($source->optionFormKey('ctID'), $types, $ctID)?>
	</div>
</div>