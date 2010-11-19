<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<div id="ccm-profile-wrapper">
    <?php  Loader::element('profile/sidebar', array('profile'=> $profile)); ?>    
    <div id="ccm-profile-body">	
    	<div id="ccm-profile-body-attributes">
    	<div class="ccm-profile-body-item">
    	
        <h1><?php echo $profile->getUserName()?></h1>
        <?php 
        $uaks = UserAttributeKey::getPublicProfileList();
        foreach($uaks as $ua) { ?>
            <div>
                <label><?php echo $ua->getKeyName()?></label>
                <?php echo $profile->getAttribute($ua, 'displaySanitized', 'display'); ?>
            </div>
        <?php  } ?>		
        
        </div>

		</div>
		
		<?php  
			$a = new Area('Main'); 
			$a->setAttribute('profile', $profile); 
			$a->setBlockWrapperStart('<div class="ccm-profile-body-item">');
			$a->setBlockWrapperEnd('</div>');
			$a->display($c); 
		?>
		
    </div>
	
	<div class="ccm-spacer"></div>
	
</div>