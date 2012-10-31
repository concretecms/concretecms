<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div id="ccm-profile-header">

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
			<a href="<?=$this->url('/account/messages/inbox', 'write', $profile->getUserID())?>" class="btn btn-mini"><i class="icon-user"></i> <?=t('Connect')?></a>		
		<? } ?>
	<? } ?>
</div>


</div>

<div id="ccm-profile-statistics-bar">
	<div class="ccm-profile-statistics-item">
		<i class="icon-time"></i> <?=t('Joined %s', date(DATE_APP_GENERIC_MDY_FULL, strtotime($profile->getUserDateAdded())))?>
	</div>
	<div class="ccm-profile-statistics-item">
		<i class="icon-fire"></i> <?=number_format(UserPointEntry::getTotal($profile))?> <?=t('Community Points')?>
	</div>
	<div class="ccm-profile-statistics-item">
		<i class="icon-bookmark"></i> <a href="#badges"><?=number_format(count($badges))?> <?=t2('Badge', 'Badges', count($badges))?></a>
	</div>
	<div class="clearfix"></div>
</div>


<div id="ccm-profile-wrapper">

	<div id="ccm-profile-detail">


        <?php 
        $uaks = UserAttributeKey::getPublicProfileList();
        foreach($uaks as $ua) { ?>
		<div>
			<h4><?php echo $ua->getKeyName()?></h4>
			<?php 
			$r = $profile->getAttribute($ua, 'displaySanitized', 'display'); 
			if ($r) {
				print $r;
			} else {
				print t('None');
			}
			?>
		</div>
        <?php  } ?>		

		<h4><?=t("Badges")?></h4>
		<? if (count($badges) > 0) { ?>


		<ul class="thumbnails">

			<? foreach($badges as $ub) { 
				$uf = $ub->getGroupBadgeImageObject();
				if (is_object($uf)) { ?>

			  <li class="span2">

			    <div class="thumbnail launch-tooltip ccm-profile-badge-image" title="<?=$ub->getGroupBadgeDescription()?>">
			      <div><img src="<?=$uf->getRelativePath()?>" /></div>
			      <div><?=t("Awarded %s", date(DATE_APP_GENERIC_MDY, strtotime($ub->getGroupDateTimeEntered($profile))))?></div>
			    </div>

			</li>

			    <? } ?>

			<? } ?>

		</ul>

		<? } else { ?>
			<p><?=t("This user hasn't won any badges.")?></p>
		<? } ?>
        
		
		<?php  
			$a = new Area('Main'); 
			$a->setAttribute('profile', $profile); 
			$a->setBlockWrapperStart('<div class="ccm-profile-body-item">');
			$a->setBlockWrapperEnd('</div>');
			$a->display($c); 
		?>
		
	</div>	
</div>

<script type="text/javascript">
$(function() {
	$(".launch-tooltip").tooltip({
		placement: 'bottom'
	});
});
</script>
