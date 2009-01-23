<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$al = Loader::helper('concrete/asset_library');
$ah = Loader::helper('concrete/interface');
?>
<style>
#ccm-slideshowBlock-imgRows a{cursor:pointer}
#ccm-slideshowBlock-imgRows .ccm-slideshowBlock-imgRow{margin-bottom:16px;clear:both;padding:7px;background-color:#eee}
#ccm-slideshowBlock-imgRows .ccm-slideshowBlock-imgRow a.moveUpLink{ display:block; background:url(<?php echo DIR_REL?>/concrete/images/icons/arrow_up.png) no-repeat center; height:10px; width:16px; }
#ccm-slideshowBlock-imgRows .ccm-slideshowBlock-imgRow a.moveDownLink{ display:block; background:url(<?php echo DIR_REL?>/concrete/images/icons/arrow_down.png) no-repeat center; height:10px; width:16px; }
#ccm-slideshowBlock-imgRows .ccm-slideshowBlock-imgRow a.moveUpLink:hover{background:url(<?php echo DIR_REL?>/concrete/images/icons/arrow_up_black.png) no-repeat center;}
#ccm-slideshowBlock-imgRows .ccm-slideshowBlock-imgRow a.moveDownLink:hover{background:url(<?php echo DIR_REL?>/concrete/images/icons/arrow_down_black.png) no-repeat center;}
#ccm-slideshowBlock-imgRows .cm-slideshowBlock-imgRowIcons{ float:right; width:35px; text-align:left; }
</style>

<div id="newImg">
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
	<td>
	<strong><?php echo t('Playback')?></strong>
	<select name="playback" style="vertical-align: middle">
		<option value="ORDER"<?php  if ($playback == 'ORDER') { ?> selected<?php  } ?>><?php echo t('Display Order')?></option>
		<option value="RANDOM-SET"<?php  if ($playback == 'RANDOM-SET') { ?> selected<?php  } ?>><?php echo t('Random (But keep sets together)')?></option>
		<option value="RANDOM"<?php  if ($playback == 'RANDOM') { ?> selected<?php  } ?>><?php echo t('Completely Random')?></option>
	</select>
	</td>
	<td style="text-align: right">
	<?php echo $ah->button_js(t('Add Image'), 'SlideshowBlock.chooseImg()');?>
	</td>
	</tr>
	</table>
</div>
<br/>

<div id="ccm-slideshowBlock-imgRows">
<?php  foreach($images as $imgInfo){ ?> 
	<?php  include($this->getBlockPath() .'/image_row_include.php'); ?> 
<?php  } ?>
</div>


<?php 
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
<?php  include($this->getBlockPath() .'/image_row_include.php'); ?> 
</div>