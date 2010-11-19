<?php 
defined('C5_EXECUTE') or die("Access Denied.");

// now that we're in the specialized content file for this block type, 
// we'll include this block type's class, and pass the block to it, and get
// the content	

$file = $controller->getFileObject();
$rel_file_path=$file->getRelativePath(); 
?>
<div style="text-align:center">

<?php 
$c = Page::getCurrentPage();
$vWidth=intval($controller->width);
$vHeight=intval($controller->height);
if ($c->isEditMode()) { ?>
	<div class="ccm-edit-mode-disabled-item" style="width:<?php echo $vWidth?>px; height:<?php echo $vHeight?>px; ">
		<div style="padding:8px 0px; padding-top: <?php echo round($vHeight/2)-10?>px;"><?php echo t('Content disabled in edit mode.')?></div>
	</div>
<?php  }else{ ?>
	
	<?php 
	
	
	//echo mime_content_type(DIR_FILES_UPLOADED.'/'.$file->getFilename()).'<br>';
	if( strstr(strtolower($file->getFilename()),'.flv') ){   ?>
		
		<script type="text/javascript">
		var flashvars = {};
		flashvars.flvfile = "<?php echo $rel_file_path?>";
		
		var params = {};
		params.menu = false;
		params.wmode="transparent";
		
		var attributes = {};
		
		swfobject.embedSWF("<?php echo $this->getBlockURL()?>/videoPlayer.swf", "flv_player_<?php echo $bID?>", "<?php echo $controller->width?>", "<?php echo $controller->height?>", "9.0.0","expressInstall.swf", flashvars, params, attributes);
		
		</script>
		
		<div class="ccm-flv-player" id="flv_player_<?php echo $bID?>">
		<?php echo t("Loading Video... If you're seeing this message you may not have Flash installed.")?>
		</div>
	
	<?php  }elseif(  strstr(strtolower($file->getFilename()),'.wmv') || strstr(strtolower($file->getFilename()),'.mpg') ||  strstr(strtolower($file->getFilename()),'.mpeg') ){ ?> 
		
		<OBJECT ID="MediaPlayer" WIDTH="<?php echo $controller->width?>" HEIGHT="<?php echo $controller->height?>" 
		CLASSID="CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95" STANDBY="Loading Windows Media Player components..." TYPE="application/x-oleobject">
		<PARAM NAME="FileName" VALUE="<?php echo $rel_file_path?>">
		<EMBED TYPE="application/x-mplayer2" pluginspage="http://www.microsoft.com/Windows/MediaPlayer/" SRC="<?php echo $rel_file_path?>" NAME="MediaPlayer" WIDTH="<?php echo $controller->width?>" HEIGHT="<?php echo $controller->height?>" ></EMBED>
		</OBJECT> 
		
	<?php  }elseif( strstr(strtolower($file->getFilename()),'.avi')  ){ ?>
	
		<OBJECT CLASSID="clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95F" HEIGHT="<?php echo $controller->height?>" WIDTH="<?php echo $controller->width?>" NAME="Msshow1" ID="Msshow1" >
			<PARAM NAME="FileName" VALUE="<?php echo $rel_file_path?>">
			<PARAM NAME="autoStart" VALUE="true">
			<PARAM NAME="showControls" VALUE="false">
			<PARAM NAME="PlayCount" VALUE="20">
			<embed src="<?php echo $rel_file_path?>" height="<?php echo $controller->height?>" width="<?php echo $controller->width?>" controller="false" autostart="true" loop=true>
			</EMBED>
		</OBJECT>
		
	<?php  }elseif( strstr(strtolower($file->getFilename()),'.mov') || strstr(strtolower($file->getFilename()),'.qt') || strstr(strtolower($file->getFilename()),'.mp4') ){ ?>	
		
		<object CLASSID="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="<?php echo $controller->width?>" height="<?php echo $controller->height?>" CODEBASE="http://www.apple.com/qtactivex/qtplugin.cab">
			<param name="src" value="<?php echo $rel_file_path?>">
			<param name="autoplay" value="true">
			<param name="loop" value="false">
			<param name="controller" value="true">
			<embed src="<?php echo $rel_file_path?>" width="<?php echo $controller->width?>" height="<?php echo $controller->height?>" autoplay="true" loop="false" controller="true" pluginspage="http://www.apple.com/quicktime/"></embed>
		</object>	 
	
	<?php  } ?>
	
<?php  } ?>
</div>