<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div id="ccm-profile-wrapper">
    <div id="ccm-profile-sidebar" style="float:left; width:20%; margin-right:5%">
        <div class="ccm-profile-header">
        	<a href="<?=View::url('/profile',$profile->getUserID())?>"><?= $av->outputUserAvatar($profile)?></a><br />
            <a href="<?=View::url('/profile',$profile->getUserID())?>"><?= $profile->getUsername()?></a>
        </div>
        <div style="margin-top:16px; padding-bottom:4px; margin-bottom:0px; font-weight:bold"><?=t('Member Since')?></div>
        <?=date('F d, Y', strtotime($profile->getUserDateAdded()))?> 
		
		<style>
		#ccm-profile-sidebar ul.nav { list-style:none; margin:0px; padding:0px; margin-top:16px;}
		</style>		
        <div>
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
    </div>
    
    <div id="ccm-profile-body" style="float:left; width:70%;">	
        <h1><?=t('My Friends') ?></h1>
        <?
		$friendsData = UsersFriends::getUsersFriendsData( $profile->getUserID() );
		if( !$friendsData ){ ?>
			<div style="padding:16px 0px;">
				<?=t('No results found.')?>
			</div>
		<? 
		}else foreach($friendsData as $friendsData){ 
			$friendUID=$friendsData['friendUID'];
			$friendUI = UserInfo::getById( $friendUID );
			?>
			<div class="ccm-users-friend" style="margin-bottom:16px;">
				<div style="float:left; width:100px;">
					<a href="<?=View::url('/profile',$friendUID)?>"><?= $av->outputUserAvatar($friendUI)?></a>
				</div>
				<div >
					<a href="<?=View::url('/profile',$friendUID) ?>"><?= $friendUI->getUsername(); ?></a>
					<div style=" font-size:90%; line-height:90%; margin-top:4px;">
					<?=t('Member Since') ?> <?=date('F d, Y', strtotime($friendUI->getUserDateAdded()))?>
					</div>
				</div>
				<div class="ccm-spacer"></div>
			</div>			
		<? } ?>	
    </div>
	
	<div class="ccm-spacer"></div>
</div>