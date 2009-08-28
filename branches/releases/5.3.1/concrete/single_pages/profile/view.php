<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div id="central" class="central-left">
    <div id="sidebar">
        <div class="ccm-profile-header">
        	<a href="<?php echo View::url('/profile',$profile->getUserID())?>"><?php echo  $av->outputUserAvatar($profile)?></a><br />
            <a href="<?php echo View::url('/profile',$profile->getUserID())?>"><?php echo  $profile->getUsername()?></a>
        </div>
        <h4 style="margin-top: 0px"><?php echo t('Member Since')?></h4>
        <?php echo date('F d, Y', strtotime($profile->getUserDateAdded()))?>
        <div>
        <?php  
		if($canEdit) {
			$bt = BlockType::getByHandle('autonav');
			$bt->controller->displayPages = 'below';
			$bt->controller->orderBy = 'display_asc';
			$bt->controller->displaySubPages = 'relevant';
			$bt->controller->displaySubPageLevels = 'enough';
			$bt->controller->displaySystemPages = true;
			$bt->render('view');
		}
		?>
        </div>
    </div>
    
    <div id="body">	
        <h1><?php echo $profile->getUserName()?></h1>
        <?php 
        $uaks = UserAttributeKey::getList();
        foreach($uaks as $ua) { ?>
            <div>
                <label><?php echo $ua->getKeyName()?></label>
                <?php echo  $ua->getUserValue($profile->getUserID()); ?>
            </div>
        <?php  } ?>		
    </div>
</div>