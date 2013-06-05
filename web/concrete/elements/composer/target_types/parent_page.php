<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');

$ctID = 0;
if (is_object($composer) && $composer->getComposerTargetTypeID() == $this->getComposerTargetTypeID()) {
	$configuredTarget = $composer->getComposerTargetObject();
	$cID = $configuredTarget->getParentPageID();
}

?>
	<div class="control-group">
		<?=$form->label('ctID', t('Publish Beneath Page'))?>
		<div class="controls">
			<? 
			$pf = Loader::helper('form/page_selector');
			print $pf->selectPage('cParentID', $cID);
			?>
		</div>
	</div>

