<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
$included = $pk->getAssignmentList(PagePermissionKey::ACCESS_TYPE_INCLUDE);
$excluded = $pk->getAssignmentList(PagePermissionKey::ACCESS_TYPE_EXCLUDE);

$excludedStr = '';
$includedStr = '';
$timeStr = '';

if (count($included) > 0) {
	for ($i = 0; $i < count($included); $i++) { 
		$class = '';
		$as = $included[$i];
		$entity = $as->getAccessEntityObject();
		$pd = $as->getPermissionDurationObject();
		if (is_object($pd)) {
			$class = 'notice';
		}
		$includedStr .= '<span class="label ' . $class . '">' . $entity->getAccessEntityLabel() . '</span> ';
	}
}
if (count($excluded) > 0) {
	for ($i = 0; $i < count($excluded); $i++) { 
		$class = '';
		$as = $excluded[$i];
		$entity = $as->getAccessEntityObject();
		$pd = $as->getPermissionDurationObject();
		if (is_object($pd)) {
			$class = 'warning';
		} else {
			$class = 'important';
		}
		$excludedStr .= '<span class="label ' . $class . '">' . $entity->getAccessEntityLabel() . '</span> ';
	}
}

?>
<? if (!$includedStr && !$excludedStr) { ?>
	<span style="color: #ccc"><?=t('None')?>
<? } else { ?>
	<?=$includedStr?> <?=$excludedStr?>
<? } ?>