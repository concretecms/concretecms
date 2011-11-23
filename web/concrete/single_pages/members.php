<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div id="ccm-profile-wrapper">
	<form method="get" action="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>">
			<?php echo t('Search');?>  		
			<input type="hidden" name="cID" value="<?=$c->getCollectionID()?>" />
			<input name="keywords" type="text" value="<?=$keywords?>" size="20" />		
			<input name="submit" type="submit" value="<?=t('Search')?>" />	

	</form>
	
	<h1><?php echo t('Members');?></h1> 	
	
	<? if ($userList->getTotal() == 0) { ?>
	
		<div><?=t('No users found.')?></div>
	
	<? } else { ?>
	
		<div class="ccm-profile-member-list">
		<?  
		$av = Loader::helper('concrete/avatar');
		$u = new User();
		
		foreach($users as $user) { 
		
			?>				
			<div class="ccm-profile-member">
				<div class="ccm-profile-member-avatar"><?=$av->outputUserAvatar($user)?></div>
				<div class="ccm-profile-member-detail">
					<div class="ccm-profile-member-username"><a href="<?=$this->url('/profile','view', $user->getUserID())?>"><?=$user->getUserName()?></a></div>
					<div class="ccm-profile-member-fields">
					<?
					foreach($attribs as $ak) { ?>
						<div>
							<?=$user->getAttribute($ak, 'displaySanitized', 'display'); ?>
						</div>
					<? } ?>
					</div>					
				</div>
				<div class="ccm-spacer"></div>
			</div>	
		
		
	
	<? } ?>
		
		</div>
		
		<?=$userList->displayPagingV2()?>
		
	<? 
	
	} ?>
</div>