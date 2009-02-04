<div id="central" class="central-left">
    <div id="sidebar">
        <div class="ccm-profile-header">
        <? print $av->outputUserAvatar($profile)?> 
        </div>
        <h4 style="margin-top: 0px"><?=t('Member Since')?></h4>
            <?=date('F d, Y', strtotime($profile->getUserDateAdded()))?>		
    </div>
    
    <div id="body">	
        <h1><?=$profile->getUserName()?></h1>
        <?
        $uaks = UserAttributeKey::getList();
        foreach($uaks as $ua) { ?>
            <div>
                <label><?=$ua->getKeyName()?></label>
                <?= $ua->getUserValue($profile->getUserID()); ?>
            </div>
        <? } ?>		
    </div>
</div>