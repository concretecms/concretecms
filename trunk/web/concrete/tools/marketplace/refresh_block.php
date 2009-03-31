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
		$btButton = t("Download");
		if (!empty($btFile)) {
			$btButton = (intval($bt->getPrice()) == 0 || $bt->isPurchase()) ? t("Install") : t("Purchase");
		}
		if (!empty($btFile) && (intval($bt->getPrice()) == 0 || $bt->isPurchase())) {
			$btLink = REL_DIR_FILES_TOOLS_REQUIRED.'/package_install?type=addon&cID=' . $bt->getRemoteCollectionID();
 			$btTarget = '';
		} else {
			$btLink = $bt->getRemoteURL();
 			$btTarget = ' target="_blank"';
		}
		?>	
		<tr class="ccm-block-type-row">
			<td valign="top"><img src="<?=$btIcon?>" /></td>
			<td>
				<div class="ccm-block-type-inner"><?=$bt->getBlockTypeName()?></div>
				<div class="ccm-block-type-description" id="ccm-bt-help<?=$bt->getBlockTypeHandle()?>"><?=$bt->getBlockTypeDescription()?></div>
			</td>
			<td><div class="ccm-block-price"><? if ($bt->getPrice() == '0.00') { print t('Free'); } else { print '$' . $bt->getPrice(); } ?></div></td>
			<td class="ccm-button-marketplace-install"><?=$ch->button($btButton, $btLink, "right");?></td>
		</tr>
	<? } ?>
		</table>
<? } else { ?>
		<p><?=t('Unable to connect to the marketplace.')?></p>
<? } ?>

	<div id="ccm-marketplace-logged-out">
		<p>You aren't currently signed in to the marketplace. <a onclick="ccmPopupLogin.show('', loginSuccess, '', 1)">Click here to sign in or create an account.</a></p>
	</div>
	<div id="ccm-marketplace-logged-in">
		<p><?=t('You are currently signed in to the marketplace as');?>
			<span id="ccm-marketplace-login-link"></span>
		  	<?=t('(Not your account? <a onclick="ccm_support.signOut(logoutSuccess)">Sign Out</a>)')?></p>
	</div>
