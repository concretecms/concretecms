<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<div id="ccm-profile-wrapper">
   <?php  Loader::element('profile/sidebar', array('profile'=> $profile)); ?>    
    <div id="ccm-profile-body">	
        <h1><?php echo t('My Friends') ?></h1>
        <?php 
		$friendsData = UsersFriends::getUsersFriendsData( $profile->getUserID() );
		if( !$friendsData ){ ?>
			<div style="padding:16px 0px;">
				<?php echo t('No results found.')?>
			</div>
		<?php  
		}else foreach($friendsData as $friendsData){ 
			$friendUID=$friendsData['friendUID'];
			$friendUI = UserInfo::getById( $friendUID );
			?>
			<div class="ccm-users-friend" style="margin-bottom:16px;">
				<div style="float:left; width:100px;">
					<a href="<?php echo View::url('/profile',$friendUID)?>"><?php echo  $av->outputUserAvatar($friendUI)?></a>
				</div>
				<div >
					<a href="<?php echo View::url('/profile',$friendUID) ?>"><?php echo  $friendUI->getUsername(); ?></a>
					<div style=" font-size:90%; line-height:90%; margin-top:4px;">
					<?php echo t('Member Since') ?> <?php echo date(DATE_APP_GENERIC_MDY_FULL, strtotime($friendUI->getUserDateAdded('user')))?>
					</div>
				</div>
				<div class="ccm-spacer"></div>
			</div>			
		<?php  } ?>	
    </div>
	
	<div class="ccm-spacer"></div>
</div>