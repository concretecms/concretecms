<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div id="ccm-profile-wrapper">
   <? Loader::element('profile/sidebar', array('profile'=> $profile)); ?>    
    <div id="ccm-profile-body">	
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
					<?=t('Member Since') ?> <?=date(DATE_APP_GENERIC_MDY_FULL, strtotime($friendUI->getUserDateAdded('user')))?>
					</div>
				</div>
				<div class="ccm-spacer"></div>
			</div>			
		<? } ?>	
    </div>
	
	<div class="ccm-spacer"></div>
</div>