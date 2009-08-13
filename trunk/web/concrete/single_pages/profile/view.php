<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div id="ccm-profile-wrapper">
    <div id="ccm-profile-sidebar" style="float:left; width:20%; margin-right:5%">
        <div class="ccm-profile-header">
        	<a href="<?=View::url('/profile',$profile->getUserID())?>"><?= $av->outputUserAvatar($profile)?></a><br />
            <a href="<?=View::url('/profile',$profile->getUserID())?>"><?= $profile->getUsername()?></a>
        </div>
        <div style="margin-top:16px; padding-bottom:4px; margin-bottom:0px; font-weight:bold"><?=t('Member Since')?></div>
        <?=date('F d, Y', strtotime($profile->getUserDateAdded('user')))?>
		
		<? 
		$u = new User();
		if( $u && $u->uID!=$profile->getUserID() ){ ?>
		<div style="margin-top:16px;">
			<? if( !UsersFriends::isFriend( $profile->getUserID(), $u->uID ) ){ ?>
				<a href="<?=View::url($c->getCollectionPath(),'add_friend','?fuID='.$profile->getUserID())?>">
					<?=t('Make this person my friend') ?>
				</a>
			<? }else{ ?>
				<a href="<?=View::url($c->getCollectionPath(),'remove_friend','?fuID='.$profile->getUserID() )?>">
					<?=t('Unfriend this person') ?>
				</a>
			<? } ?>
		</div>
		<? } ?>
		
		<style>
		#ccm-profile-sidebar ul.nav { list-style:none; margin:0px; padding:0px; margin-top:16px;}
		</style>		
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
    
    <div id="ccm-profile-body" style="float:left; width:70%;">	
        <h1><?=$profile->getUserName()?></h1>
        <?
        $uaks = UserAttributeKey::getPublicProfileList();
        foreach($uaks as $ua) { ?>
            <div>
                <label><?=$ua->getKeyName()?></label>
                <?=$profile->getAttribute($ua); ?>
            </div>
        <? } ?>		
    </div>
	
	<div class="ccm-spacer"></div>
</div>