<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="page-header" id="ccm-profile-header">

<div id="ccm-profile-avatar">
<? print Loader::helper('concrete/avatar')->outputUserAvatar($profile); ?>
</div>

<h1><?=$profile->getUserName()?></h1>

<div id="ccm-profile-controls">
	<? if ($canEdit) { ?>
		<div class="btn-group">
			<a href="<?=$this->url('/account/profile/edit')?>" class="btn btn-mini"><i class="icon-cog"></i> <?=t('Edit')?></a>
			<a href="<?=$this->url('/account')?>" class="btn btn-mini"><i class="icon-home"></i> <?=t('Home')?></a>
		</div>
	<? } else { ?>
		<? if ($profile->getAttribute('profile_private_messages_enabled')) { ?>
			<a href="#" class="btn btn-mini"><i class="icon-user"></i> <?=t('Connect')?></a>		
		<? } ?>
	<? } ?>
</div>


</div>



<div id="ccm-profile-wrapper">

	<div id="ccm-profile-detail">
		
				
		<div>		
		<h4><?=t('Member Since')?></h4>
		<?=date('M d, Y', strtotime($profile->getUserDateAdded()))?>
		</div>

        <?php 
        $uaks = UserAttributeKey::getPublicProfileList();
        foreach($uaks as $ua) { ?>
		<div>
			<h4><?php echo $ua->getKeyName()?></h4>
			<?php echo $profile->getAttribute($ua, 'displaySanitized', 'display'); ?>
		</div>
        <?php  } ?>		
        
		
		<?php  
			$a = new Area('Main'); 
			$a->setAttribute('profile', $profile); 
			$a->setBlockWrapperStart('<div class="ccm-profile-body-item">');
			$a->setBlockWrapperEnd('</div>');
			$a->display($c); 
		?>
		
	</div>	
</div>