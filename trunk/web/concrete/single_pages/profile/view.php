<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div id="ccm-profile-wrapper">
    <? Loader::element('profile/sidebar', array('profile'=> $profile)); ?>    
    <div id="ccm-profile-body">	
        <h1><?=$profile->getUserName()?></h1>
        <?
        $uaks = UserAttributeKey::getPublicProfileList();
        foreach($uaks as $ua) { ?>
            <div>
                <label><?=$ua->getKeyName()?></label>
                <?=$profile->getAttribute($ua, 'display'); ?>
            </div>
        <? } ?>		
    </div>
	
	<div class="ccm-spacer"></div>
</div>