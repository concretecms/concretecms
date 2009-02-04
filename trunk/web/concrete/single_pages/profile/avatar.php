<? $this->inc('elements/profile_header.php'); ?>

<h2>User Avatar</h2>
<p>Change the picture attached to my posts. </p>

			
<div style="position:relative; width:100%; height:500px ;">		
	<div style="position: absolute; left:-40px; top:0px; width:450px; height:500px;">
	<div id="discussion-profile-avatar"> 	
		<div style="margin-left: 40px">
		You need the Adobe Flash plugin installed on your computer to upload and crop your user profile picture.
		<br /><Br />
		<a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Download the Flash Player here</a>.
		</div>
	</div>
	<? if ($ui->hasAvatar()) { ?>
		<div style="margin-left:40px;">
		<a href="<?=$this->action('delete')?>">Remove your user avatar &gt;</a>
		</div>
	<? } ?>	
	</div>

	<div class="spacer"></div>
	
    <script>
	$(function(){   

		//SWF OBJECT 2.0 Method
		var params = { 
			bgcolor: "#ffffff",
			wmode:  "transparent",
			quality:  "high" 
		};
		var flashvars = { 
			bgcolor: "#ffffff",
			base_url: "<?=BASE_URL?>",
			image: "<?=$_REQUEST['tmp_avatar']?REL_DIR_FILES_UPLOADED ."/up_tmp/".$_REQUEST['tmp_avatar']: $av->getImagePath($ui)?>",
			session: "<?=session_id()?>",
			thumbwidth: <?=AVATAR_WIDTH?>,
			thumbheight: <?=AVATAR_HEIGHT?>,
			uploadscript: "<?=$this->url($c->getCollectionPath(), 'upload')?>",
			"export": "<?=$this->url($c->getCollectionPath(), 'save_thumb')?>" 
		};
		swfobject.embedSWF("<?=DIR_REL?>/flash/thumbnail_editor.swf", "discussion-profile-avatar", "450", "500", "8.0", false, flashvars, params);
	});
	</script>
</div>
<? $this->inc('elements/profile_footer.php'); ?>
