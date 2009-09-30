<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div id="ccm-profile-wrapper">
    <?php  Loader::element('profile/sidebar', array('profile'=> $profile)); ?>    
    <div id="ccm-profile-body">	
        <h1><?php echo $profile->getUserName()?></h1>
        <?php 
        $uaks = UserAttributeKey::getPublicProfileList();
        foreach($uaks as $ua) { ?>
            <div>
                <label><?php echo $ua->getKeyName()?></label>
                <?php echo $profile->getAttribute($ua, 'display'); ?>
            </div>
        <?php  } ?>		
    </div>
	
	<div class="ccm-spacer"></div>
</div>