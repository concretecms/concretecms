<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$url = parse_url($videoURL);
parse_str($url['query'], $query);
$c = Page::getCurrentPage();
 
$vWidth=425;
$vHeight=344;
if ($c->isEditMode()) { ?>
	<div class="ccm-edit-mode-disabled-item" style="width:<?php echo $vWidth?>px; height:<?php echo $vHeight?>px;">
		<div style="padding:8px 0px; padding-top: <?php echo round($vHeight/2)-10?>px;"><?php echo t('Content disabled in edit mode.')?></div>
	</div>
<?php  }else{ ?>
<object width="<?php echo $vWidth?>" height="<?php echo $vHeight?>">
	<param name="movie" value="http://www.youtube.com/v/<?php echo $query['v']?>&hl=en" />
	<param name="wmode" value="transparent" />
	<embed src="http://www.youtube.com/v/<?php echo $query['v']?>&hl=en" type="application/x-shockwave-flash" wmode="transparent" width="425" height="344" />
</object>
<?php  } ?>