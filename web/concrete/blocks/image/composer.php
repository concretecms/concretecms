<?php defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$html = Loader::helper('html');
$url = Loader::helper('concrete/urls');

if ($controller->getFileID() > 0) { 
	$bf = $controller->getFileObject();
}

$al = Loader::helper('concrete/asset_library');

?>

<div class="control-group">
	<label class="control-label"><?=$label?></label>
	<div class="controls">
		<?php echo $al->image('ccm-b-image-'.$bID, $this->field('fID'), t('Choose Image'), $bf); ?>
	</div>
</div>