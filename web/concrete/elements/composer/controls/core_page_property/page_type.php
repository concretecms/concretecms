<?
defined('C5_EXECUTE') or die("Access Denied.");
$types = array();
$composer = $set->getComposerObject();
foreach($composer->getComposerPageTypeObjects() as $type) {
	$types[$type->getCollectionTypeID()] = $type->getCollectionTypeName();
}
?>

<div class="control-group">
	<label class="control-label"><?=$label?></label>
	<div class="controls" data-composer-field="page_type">
		<?=$form->select('cmpPageTypeID', $types, $control->getComposerControlDraftValue())?>
	</div>
</div>