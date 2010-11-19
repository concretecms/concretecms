<?php  
defined('C5_EXECUTE') or die("Access Denied.");
if ((!$width) || (!$height)) {
	$height = 200;
}
?>
<div class="ccm-edit-mode-disabled-item" style="<?php  if ($width && $height) { ?>width:<?php echo $width?>px;<?php  } ?>height:<?php echo $height?>px;">
	<div style="padding:8px 0px; padding-top: <?php echo round($height/2)-10?>px;"><?php echo t('Content disabled in edit mode.')?></div>
</div>
