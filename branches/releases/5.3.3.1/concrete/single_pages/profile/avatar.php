<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div id="ccm-profile-wrapper">
    <?php  Loader::element('profile/sidebar', array('profile'=> $ui)); ?>    
    <div id="ccm-profile-body">	

        <h2><?php echo t('User Avatar')?></h2>
        <p><?php echo t('Change the picture attached to my posts.')?></p>
        
                    
        <div style="position:relative; width:100%; height:500px ;">		
            <div style="position: absolute; left:-40px; top:0px; width:450px; height:500px;">
            <div id="discussion-profile-avatar"> 	
                <div style="margin-left: 40px">
                <?php echo t('You need the Adobe Flash plugin installed on your computer to upload and crop your user profile picture.')?>
                <br /><br />
                <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Download the Flash Player here</a>.
                </div>
            </div>
            <?php  if ($ui->hasAvatar()) { ?>
                <div style="margin-left:40px;">
                <a href="<?php echo $this->action('delete')?>"><?php echo t('Remove your user avatar &gt;')?></a>
                </div>
            <?php  } ?>	
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
                    base_url: "<?php echo BASE_URL?>",
                    image: "<?php echo $_REQUEST['tmp_avatar']?REL_DIR_FILES_UPLOADED ."/up_tmp/".$_REQUEST['tmp_avatar']: $av->getImagePath($ui)?>",
                    session: "<?php echo session_id()?>",
                    thumbwidth: <?php echo AVATAR_WIDTH?>,
                    thumbheight: <?php echo AVATAR_HEIGHT?>,
                    uploadscript: "<?php echo $this->url($c->getCollectionPath(), 'upload')?>",
                    "export": "<?php echo $this->url($c->getCollectionPath(), 'save_thumb')?>" 
                };
                swfobject.embedSWF("<?php echo DIR_REL?>/concrete/flash/thumbnail_editor.swf", "discussion-profile-avatar", "450", "500", "8.0", false, flashvars, params);
            });
            </script>
        </div>
	</div>
	
	<div class="ccm-spacer"></div>
</div>