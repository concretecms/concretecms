<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div id="ccm-profile-wrapper">
    <? Loader::element('profile/sidebar', array('profile'=> $profile)); ?>    
    <div id="ccm-profile-body">	
    	<div id="ccm-profile-body-attributes">
    	<div class="ccm-profile-body-item">
    	
        <h1><?=$profile->getUserName()?></h1>
        <?
        $uaks = UserAttributeKey::getPublicProfileList();
        foreach($uaks as $ua) { ?>
            <div>
                <label><?=tc('AttributeKeyName', $ua->getAttributeKeyName())?></label>
                <?=$profile->getAttribute($ua, 'displaySanitized', 'display'); ?>
            </div>
        <? } ?>		
        
        </div>

		</div>
		
		<? 
			$a = new Area('Main'); 
			$a->setAttribute('profile', $profile); 
			$a->setBlockWrapperStart('<div class="ccm-profile-body-item">');
			$a->setBlockWrapperEnd('</div>');
			$a->display($c); 
		?>
		
    </div>
	
	<div class="ccm-spacer"></div>
	
</div>