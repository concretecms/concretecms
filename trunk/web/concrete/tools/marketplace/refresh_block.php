<?  defined('C5_EXECUTE') or die(_("Access Denied."));

$ch = Loader::helper('concrete/interface');

//marketplace
if(ENABLE_MARKETPLACE_SUPPORT){
	$marketplaceBlocksHelper = Loader::helper('concrete/marketplace/blocks'); 
	$marketplaceBlockTypes = $marketplaceBlocksHelper->getCombinedList();
}else{
	$marketplaceBlockTypes=array();
}

?>
<? if (count($marketplaceBlockTypes) > 0) { ?>

	<table class="ccm-block-type-table">

	<? foreach($marketplaceBlockTypes as $bt) { 
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
			<td<?=!empty($btDesc)?' valign="top"':''?>><img src="<?=$btIcon?>" /></td>
			<td width="90%">
				<div class="ccm-block-type-inner"><?=$bt->getBlockTypeName()?></div>
			<? if (!empty($btDesc)) { ?>
				<div class="ccm-block-type-description" id="ccm-bt-help<?=$bt->getBlockTypeHandle()?>"><?=$btDesc?></div>
			<? } ?>
			</td>
			<td><div class="ccm-block-price"><? if ($bt->getPrice() == '0.00') { print t('Free'); } else { print '$' . $bt->getPrice(); } ?></div></td>
			<td<?=$btClass?>><?=$ch->button($btButton, $btLink, "right");?></td>
		</tr>
	<? } ?>
		</table>
<? } else { ?>
		<p><?=t('Unable to connect to the marketplace.')?></p>
<? } ?>

	<div id="ccm-marketplace-logged-out">
		<p><?=t('You are not currently signed in to the marketplace.')?> <a onclick="ccmPopupLogin.show('', ccm_loginSuccess, '', 1)"><?=t('Click here to sign in or create an account.')?></a></p>
	</div>
	<div id="ccm-marketplace-logged-in">
		<p><?=t('You are currently signed in to the marketplace as ');?>
          	<a href="<?=CONCRETE5_ORG_URL ?>/profile/-/<?=UserInfo::getRemoteAuthUserId() ?>/" ><?=UserInfo::getRemoteAuthUserName() ?></a>
		  	<?=t('(Not your account? <a onclick="ccm_support.signOut(ccm_logoutSuccess)">Sign Out</a>)')?></p>
	</div>
