<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$al = Loader::helper('concrete/asset_library');
$ah = Loader::helper('concrete/interface');
?>
<style>
#ccm-slideshowBlock-imgRows a{cursor:pointer}
#ccm-slideshowBlock-imgRows .ccm-slideshowBlock-imgRow{margin-bottom:16px;clear:both;padding:7px;background-color:#eee}
#ccm-slideshowBlock-imgRows .ccm-slideshowBlock-imgRow a.moveUpLink{ display:block; background:url(<?=DIR_REL?>/concrete/images/icons/arrow_up.png) no-repeat center; height:10px; width:16px; }
#ccm-slideshowBlock-imgRows .ccm-slideshowBlock-imgRow a.moveDownLink{ display:block; background:url(<?=DIR_REL?>/concrete/images/icons/arrow_down.png) no-repeat center; height:10px; width:16px; }
#ccm-slideshowBlock-imgRows .ccm-slideshowBlock-imgRow a.moveUpLink:hover{background:url(<?=DIR_REL?>/concrete/images/icons/arrow_up_black.png) no-repeat center;}
#ccm-slideshowBlock-imgRows .ccm-slideshowBlock-imgRow a.moveDownLink:hover{background:url(<?=DIR_REL?>/concrete/images/icons/arrow_down_black.png) no-repeat center;}
#ccm-slideshowBlock-imgRows .cm-slideshowBlock-imgRowIcons{ float:right; width:35px; text-align:left; }
</style>

<div id="newImg">
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
	<td>
	<strong><?=t('Playback')?></strong>
	<select name="playback" style="vertical-align: middle">
		<option value="ORDER"<? if ($playback == 'ORDER') { ?> selected<? } ?>><?=t('Display Order')?></option>
		<option value="RANDOM-SET"<? if ($playback == 'RANDOM-SET') { ?> selected<? } ?>><?=t('Random (But keep sets together)')?></option>
		<option value="RANDOM"<? if ($playback == 'RANDOM') { ?> selected<? } ?>><?=t('Completely Random')?></option>
	</select>
	</td>
	<td style="text-align: right">
	<?=$ah->button_js(t('Add Image'), 'SlideshowBlock.chooseImg()');?>
	</td>
	</tr>
	</table>
</div>
<br/>

<div id="ccm-slideshowBlock-imgRows">
<? foreach($images as $imgInfo){ ?> 
<? $this->inc('image_row_include.php', array('imgInfo' => $imgInfo)); ?> 
<? } ?>
</div>


<?
$imgInfo['slideshowImgId']='tempSlideshowImgId';
$imgInfo['image_bID']='tempBID';
$imgInfo['fileName']='tempFilename';
$imgInfo['origfileName']='tempOrigFilename';
$imgInfo['thumbPath']='tempThumbPath';
$imgInfo['duration']=$defaultDuration;
$imgInfo['fadeDuration']=$defaultFadeDuration;
$imgInfo['groupSet']=0;
$imgInfo['imgHeight']=tempHeight;
$imgInfo['url']='';
?>
<div id="imgRowTemplateWrap" style="display:none">
<? $this->inc('image_row_include.php', array('imgInfo' => $imgInfo)); ?> 
</div>