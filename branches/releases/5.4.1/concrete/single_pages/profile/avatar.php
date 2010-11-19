<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<div id="ccm-profile-wrapper">
    <?php  Loader::element('profile/sidebar', array('profile'=> $ui)); ?>    
    <div id="ccm-profile-body">	

        <h2><?php echo t('User Avatar')?></h2>
        <p><?php echo t('Change the picture attached to my posts.')?></p>
        
                    
        <div style="position:relative; width:100%; height:500px ;">		
            <div id="profile-avatar"> 	
                <?php echo t('You need the Adobe Flash plugin installed on your computer to upload and crop your user profile picture.')?>
                <br /><br />
                <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Download the Flash Player here</a>.
            </div>
            <?php  if ($ui->hasAvatar()) { ?>
				<br/><br/>
                <a href="<?php echo $this->action('delete')?>"><?php echo t('Remove your user avatar &gt;')?></a>

            <?php  } ?>	
        
            <div class="spacer"></div>
            
            <script type="text/javascript">
           	ThumbnailBuilder_onSaveCompleted = function() { 
				alert("<?php echo t('User Profile picture saved.')?>");
				window.location.href="<?php echo $this->url('/profile/avatar')?>";
			}
			
            $(function(){   
                var params = { 
                    bgcolor: "#ffffff",
                    wmode:  "transparent",
                    quality:  "high" 
                };
				var flashvars = {
                    width: '<?php echo AVATAR_WIDTH?>',
                    height: '<?php echo AVATAR_HEIGHT?>',
                    image: '<?php echo $av->getImagePath($ui)?>',
                    save: "<?php echo $this->url($c->getCollectionPath(), 'save_thumb')?>"
                };
				swfobject.embedSWF ("<?php echo DIR_REL?>/concrete/flash/thumbnail_editor_2.swf", "profile-avatar", "500", "400", "10,0,0,0", "includes/expressInstall.swf", flashvars, params);
        
           		
           });
            </script>
        </div>
	</div>
	
	<div class="ccm-spacer"></div>
</div>