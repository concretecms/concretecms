<? $av = Loader::helper('concrete/avatar'); ?>
<div id="ccm-profile-sidebar">
	<div class="ccm-profile-header">
		<a href="<?=View::url('/profile',$profile->getUserID())?>"><?= $av->outputUserAvatar($profile)?></a><br />
		<a href="<?=View::url('/profile',$profile->getUserID())?>"><?= $profile->getUsername()?></a>
	</div>
	<div style="margin-top:16px; padding-bottom:4px; margin-bottom:0px; font-weight:bold"><?=t('Member Since')?></div>
	<?=date(DATE_APP_GENERIC_MDY_FULL, strtotime($profile->getUserDateAdded('user')))?>
	
	<? 
	$u = new User();
	if ($u->isRegistered() && $u->getUserID() != $profile->getUserID()) { ?>
	<div style="margin-top:16px;">
		<div>
		<? if( !UsersFriends::isFriend( $profile->getUserID(), $u->uID ) ){ ?>
			<a href="<?=View::url('/profile/friends','add_friend', $profile->getUserID())?>">
				<?=t('Add to My Friends') ?>
			</a>
		<? }else{ ?>
			<a href="<?=View::url('/profile/friends','remove_friend', $profile->getUserID() )?>">
				<?=t('Remove from My Friends') ?>
			</a>
		<? } ?>
		
		</div>
		<? if ($profile->getUserProfilePrivateMessagesEnabled() == 1) { ?>
			<a href="<?=$this->url('/profile/messages', 'write', $profile->getUserID())?>"><?=t('Send Private Message')?></a>	
		<? } ?>
		
	</div>
	<? } ?>

	
	<div>
	<? 
	if($u->getUserID() == $profile->getUserID()) {
		$nc = Page::getByPath('/profile');
		$pl = new PageList();
		$pl->filterByParentID($nc->getCollectionID());
		$pages = $pl->get(0);
		if (is_array($pages) && !empty($pages)) {
			$nh = Loader::helper('navigation');
			?>
			<ul class="nav">
			<?php foreach ($pages as $page) { ?>
				<li><a href="<?php echo $nh->getLinkToCollection($page) ?>"><?php echo t($page->getCollectionName())?></a></li>
			<?php } ?>
			</ul>
		<?php
		}
	}
	?>
	</div>

		<form method="get" action="<?=$this->url('/members')?>">
		<h4><?=t('Search Members')?></h4>
		<?
		$form = Loader::helper('form');
		print $form->text('keywords', array('style' => 'width: 80px'));
		print '&nbsp;&nbsp;';
		print $form->submit('submit', t('Search'));
		?>
		
		</form>

</div>
