<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$ctArray = CollectionType::getList();
$types = array();
foreach($ctArray as $cta) {
    $types[$cta->getCollectionTypeID()] = $cta->getCollectionTypeName();
}
?>
	<div class="control-group">
		<?=$form->label('ctID', t('Publish Page Type'))?>
		<div class="controls">
			<?=$form->select('ctID', $types)?>
		</div>
	</div>