<?php defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Page\Type\Type as PageType;

$ctArray = PageType::getList();
$types = array();
foreach ($ctArray as $ct) {
    $types[$ct->getPageTypeID()] = $ct->getPageTypeDisplayName();
}

if (is_object($configuration)) {
    $ptID = $configuration->getPageTypeID();
}

?>
<div class="control-group">
	<label class="control-label"><?=t('Limit By Page Type')?></label>
	<div class="controls" data-select="page">
		<?=$form->select($source->optionFormKey('ptID'), $types, $ptID)?>
	</div>
</div>