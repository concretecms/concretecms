<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
$assignments = $pk->getAccessList(PermissionKey::ACCESS_TYPE_ALL);

$str = '';

if (count($assignments) > 0) {
	for ($i = 0; $i < count($assignments); $i++) { 
		$class = '';
		$as = $assignments[$i];
		$entity = $as->getAccessEntityObject();
		$pd = $as->getPermissionDurationObject();
		if ($as->getAccessType() == PermissionKey::ACCESS_TYPE_EXCLUDE) {
			if (is_object($pd)) {
				$class = 'warning';
			} else {
				$class = 'important';
			}
		} else { 
			if (is_object($pd)) {
				$class = 'notice';
			}
		}
		$str .= '<span class="label ' . $class . '">' . $entity->getAccessEntityLabel() . '</span> ';
	}
}

?>
<? if (!$str) { ?>
	<span style="color: #ccc"><?=t('None')?>
<? } else { ?>
	<?=$str?>
<? } ?>