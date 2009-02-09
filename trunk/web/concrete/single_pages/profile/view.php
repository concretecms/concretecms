<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div id="central" class="central-left">
    <div id="sidebar">
        <div class="ccm-profile-header">
        	<a href="<?=View::url('/profile',$profile->getUserID())?>"><?= $av->outputUserAvatar($profile)?></a><br />
            <a href="<?=View::url('/profile',$profile->getUserID())?>"><?= $profile->getUsername()?></a>
        </div>
        <h4 style="margin-top: 0px"><?=t('Member Since')?></h4>
        <?=date('F d, Y', strtotime($profile->getUserDateAdded()))?>
        <div>
        <? 
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