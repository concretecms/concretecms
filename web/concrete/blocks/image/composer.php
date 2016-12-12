<?php defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$html = Loader::helper('html');
$url = Loader::helper('concrete/urls');

if ($controller->getFileID() > 0) { 
	$bf = $controller->getFileObject();
}

$setcontrol = $control->getPageTypeComposerFormLayoutSetControlObject();
$al = Loader::helper('concrete/asset_library');

?>

<div class="control-group">
	<label class="control-label"><?=$label?></label>
	<? if($description): ?>
	<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?=$description?>"></i>
	<? endif; ?>
	<div class="controls">
		<?php echo $al->image('ccm-b-image-'.$setcontrol->getPageTypeComposerFormLayoutSetControlID(), $view->field('fID'), t('Choose Image'), $bf); ?>
	</div>
</div>