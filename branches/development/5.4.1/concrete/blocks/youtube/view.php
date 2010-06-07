<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$url = parse_url($videoURL);
parse_str($url['query'], $query);
$c = Page::getCurrentPage();
 
$vWidth=425;
$vHeight=344;
if ($c->isEditMode()) { ?>
	<div class="ccm-edit-mode-disabled-item" style="width:<?=$vWidth?>px; height:<?=$vHeight?>px;">
		<div style="padding:8px 0px; padding-top: <?=round($vHeight/2)-10?>px;"><?=t('Content disabled in edit mode.')?></div>
	</div>
<? }else{ ?>
<object width="<?=$vWidth?>" height="<?=$vHeight?>">
	<param name="movie" value="http://www.youtube.com/v/<?=$query['v']?>&hl=en" />
	<param name="wmode" value="transparent" />
	<embed src="http://www.youtube.com/v/<?=$query['v']?>&hl=en" type="application/x-shockwave-flash" wmode="transparent" width="425" height="344" />
</object>
<? } ?>