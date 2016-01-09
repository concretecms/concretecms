<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');

$cParentID = 0;
$selectorFormFactor = '';
if (is_object($pagetype) && $pagetype->getPageTypePublishTargetTypeID() == $type->getPageTypePublishTargetTypeID()) {
	$configuredTarget = $pagetype->getPageTypePublishTargetObject();
	$cID = $configuredTarget->getParentPageID();
}

?>
	<div class="control-group">
		<?=$form->label('cParentID', t('Publish Beneath Page'))?>
		<div class="controls">
			<?php
			$pf = Loader::helper('form/page_selector');
			print $pf->selectPage('cParentID', $cID);
			?>
		</div>
	</div>
