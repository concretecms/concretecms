<?php
defined('C5_EXECUTE') or die('Access Denied.');

$noneText = t('** Select Group');
$selectGroups = ['' => $noneText];
foreach($groups as $group) {
    $selectGroups[$group->getGroupID()] = $group->getGroupDisplayName();
}
echo $form->select($view->field('value'), $selectGroups, $value);
