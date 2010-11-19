<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<div id="ccm-profile-wrapper">
	<form method="get" action="<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>">
			Search  		
			<input type="hidden" name="cID" value="<?php echo $c->getCollectionID()?>" />
			<input name="keywords" type="text" value="<?php echo $keywords?>" size="20" />		
			<input name="submit" type="submit" value="<?php echo t('Search')?>" />	

	</form>
	
	<h1>Members</h1> 	
	
	<?php  if ($userList->getTotal() == 0) { ?>
	
		<div><?php echo t('No users found.')?></div>
	
	<?php  } else { ?>
	
		<div class="ccm-profile-member-list">
		<?php   
		$av = Loader::helper('concrete/avatar');
		$u = new User();
		
		foreach($users as $user) { 
		
			?>				
			<div class="ccm-profile-member">
				<div class="ccm-profile-member-avatar"><?php echo $av->outputUserAvatar($user)?></div>
				<div class="ccm-profile-member-detail">
					<div class="ccm-profile-member-username"><a href="<?php echo $this->url('/profile','view', $user->getUserID())?>"><?php echo $user->getUserName()?></a></div>
					<div class="ccm-profile-member-fields">
					<?php 
					foreach($attribs as $ak) { ?>
						<div>
							<?php echo $user->getAttribute($ak, 'displaySanitized', 'display'); ?>
						</div>
					<?php  } ?>
					</div>					
				</div>
				<div class="ccm-spacer"></div>
			</div>	
		
		
	
	<?php  } ?>
		
		</div>
		
		<?php echo $userList->displayPaging()?>
		
	<?php  
	
	} ?>
</div>