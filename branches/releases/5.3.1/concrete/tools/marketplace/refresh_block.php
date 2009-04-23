<?php   defined('C5_EXECUTE') or die(_("Access Denied."));

$ch = Loader::helper('concrete/interface');

//marketplace
if(ENABLE_MARKETPLACE_SUPPORT){
	$marketplaceBlocksHelper = Loader::helper('concrete/marketplace/blocks'); 
	$marketplaceBlockTypes = $marketplaceBlocksHelper->getCombinedList();
}else{
	$marketplaceBlockTypes=array();
}

?>
<?php  if (count($marketplaceBlockTypes) > 0) { ?>

	<table class="ccm-block-type-table">

	<?php  foreach($marketplaceBlockTypes as $bt) { 
		$btIcon = $bt->getRemoteIconURL();
		$btFile = $bt->getRemoteFileURL();
		if (empty($btFile)) continue;
		$btButton = t("Download");
		$btClass = "";
		$btDesc = $bt->getBlockTypeDescription();
		if (!empty($btFile)) {
			if (intval($bt->getPrice()) == 0 || $bt->isPurchase()) {
				$btButton = t("Install");
				$btClass = ' class="ccm-button-marketplace-install"';
			} else {
 				$btButton = t("Purchase");
			}
		}
		if (!empty($btFile) && (intval($bt->getPrice()) == 0 || $bt->isPurchase())) {
			$btLink = REL_DIR_FILES_TOOLS_REQUIRED.'/package_install?type=addon&install=1&cID=' . $bt->getRemoteCollectionID();
 			$btTarget = '';
		} else {
			$btLink = $bt->getRemoteURL();
 			$btTarget = ' target="_blank"';
		}
		?>	
		<tr class="ccm-block-type-row">
			<td<?php echo !empty($btDesc)?' valign="top"':''?>><img src="<?php echo $btIcon?>" /></td>
			<td width="90%">
				<div class="ccm-block-type-inner"><?php echo $bt->getBlockTypeName()?></div>
			<?php  if (!empty($btDesc)) { ?>
				<div class="ccm-block-type-description" id="ccm-bt-help<?php echo $bt->getBlockTypeHandle()?>"><?php echo $btDesc?></div>
			<?php  } ?>
			</td>
			<td><div class="ccm-block-price"><?php  if ($bt->getPrice() == '0.00') { print t('Free'); } else { print '$' . $bt->getPrice(); } ?></div></td>
			<td<?php echo $btClass?>><?php echo $ch->button($btButton, $btLink, "right", NULL, array('target'=>'_blank') );?></td>
		</tr>
	<?php  } ?>
		</table>
<?php  } else { ?>
		<p><?php echo t('Unable to connect to the marketplace.')?></p>
<?php  } ?>

	<div id="ccm-marketplace-logged-out">
		<p><?php echo t('You are not currently signed in to the marketplace.')?> <a onclick="ccmPopupLogin.show('', ccm_loginSuccess, '', 1)"><?php echo t('Click here to sign in or create an account.')?></a></p>
	</div>
	<div id="ccm-marketplace-logged-in">
		<p><?php echo t('You are currently signed in to the marketplace as ');?>
          	<a href="<?php echo CONCRETE5_ORG_URL ?>/profile/-/<?php echo UserInfo::getRemoteAuthUserId() ?>/" ><?php echo UserInfo::getRemoteAuthUserName() ?></a>
		  	<?php echo t('(Not your account? <a onclick="ccm_support.signOut(ccm_logoutSuccess)">Sign Out</a>)')?></p>
	</div>
