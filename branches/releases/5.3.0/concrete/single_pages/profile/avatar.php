<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div id="central" class="central-left">
    <div id="sidebar">
    	<div class="ccm-profile-header">
        	<a href="<?php echo View::url('/profile',$ui->getUserID())?>"><?php echo  $av->outputUserAvatar($ui)?></a><br />
            <a href="<?php echo View::url('/profile',$ui->getUserID())?>"><?php echo  $ui->getUsername()?></a>
        </div>
        <h4 style="margin-top: 0px"><?php echo t('Member Since')?></h4>
        <?php echo date('F d, Y', strtotime($ui->getUserDateAdded()))?>
        <?php  
		$bt = BlockType::getByHandle('autonav');
		$bt->controller->displayPages = 'current';
		$bt->controller->orderBy = 'display_asc';
		$bt->controller->displaySubPages = 'relevant';
		$bt->controller->displaySubPageLevels = 'enough';
		$bt->controller->displaySystemPages = true;
		$bt->render('view');
		?>
    </div>
    
    <div id="body">	

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
</div>