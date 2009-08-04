<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div id="ccm-profile-wrapper">
    <div id="ccm-profile-sidebar" style="float:left; width:20%; margin-right:5%">
    	<div class="ccm-profile-header">
        	<a href="<?=View::url('/profile',$ui->getUserID())?>"><?= $av->outputUserAvatar($ui)?></a><br />
            <a href="<?=View::url('/profile',$ui->getUserID())?>"><?= $ui->getUsername()?></a>
        </div>
        <h4 style="margin-top: 0px"><?=t('Member Since')?></h4>
        <?=date('F d, Y', strtotime($ui->getUserDateAdded()))?>
		
		<style>
		#ccm-profile-sidebar ul.nav { list-style:none; margin:0px; padding:0px; margin-top:16px;}
		</style>
        <? 
		$bt = BlockType::getByHandle('autonav');
		$bt->controller->displayPages = 'current';
		$bt->controller->orderBy = 'display_asc';
		$bt->controller->displaySubPages = 'relevant';
		$bt->controller->displaySubPageLevels = 'enough';
		$bt->controller->displaySystemPages = true;
		$bt->render('view');
		?>
    </div>
    
    <div id="ccm-profile-body" style="float:left; width:70%;">	

        <h2><?=t('User Avatar')?></h2>
        <p><?=t('Change the picture attached to my posts.')?></p>
        
                    
        <div style="position:relative; width:100%; height:500px ;">		
            <div style="position: absolute; left:-40px; top:0px; width:450px; height:500px;">
            <div id="discussion-profile-avatar"> 	
                <div style="margin-left: 40px">
                <?=t('You need the Adobe Flash plugin installed on your computer to upload and crop your user profile picture.')?>
                <br /><br />
                <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Download the Flash Player here</a>.
                </div>
            </div>
            <? if ($ui->hasAvatar()) { ?>
                <div style="margin-left:40px;">
                <a href="<?=$this->action('delete')?>"><?=t('Remove your user avatar &gt;')?></a>
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
                swfobject.embedSWF("<?=DIR_REL?>/concrete/flash/thumbnail_editor.swf", "discussion-profile-avatar", "450", "500", "8.0", false, flashvars, params);
            });
            </script>
        </div>
	</div>
	
	<div class="ccm-spacer"></div>
</div>