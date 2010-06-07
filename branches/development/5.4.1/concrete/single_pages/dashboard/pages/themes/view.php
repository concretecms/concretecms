<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$bt = Loader::helper('concrete/interface');
$valt = Loader::helper('validation/token');

$alreadyActiveMessage = t('This theme is currently active on your site.');

?>

<h1><span><?=t('Themes')?></span></h1>
<div class="ccm-dashboard-inner">
	
	
	<? if (isset($activate_confirm)) { ?>
	<strong><?=t('Are you sure you wish to activate this theme? Any custom theme selections across your site will be reset.')?></strong>
	<br/><br/>
	<?=$bt->button(t("Yes, activate this theme."), $activate_confirm, "left");?>
	<?=t('or')?> <a href="<?=$this->url('/dashboard/pages/themes/')?>"><?=t('Cancel')?></a>
	
	<div class="ccm-spacer">&nbsp;</div>
	
	<? } else { ?>
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table border="0" cellspacing="0" cellpadding="0" id="ccm-template-list">
	<?
	if (count($tArray) == 0) { ?>
	<tr>
		<td colspan="5"><?=t('No themes are available.')?></td>
	</tr>
	<? } else {
		foreach ($tArray as $t) { ?>
		<tr <? if ($siteThemeID == $t->getThemeID()) { ?> class="ccm-theme-active" <? } ?>>
			<td><?=$t->getThemeThumbnail()?></td>
			<td class="ccm-template-content">
			<h2><?=$t->getThemeName()?></h2>
			<?=$t->getThemeDescription()?>
			<br/><br/>
			<? if ($siteThemeID == $t->getThemeID()) { ?>
				<?=$bt->button_js(t("Activate"), "alert('" . $alreadyActiveMessage . "')", "left", "ccm-button-inactive");?>
			<? } else { ?>
				<?=$bt->button(t("Activate"), $this->url('/dashboard/pages/themes','activate', $t->getThemeID()), "left");?>
			<? } ?>
			<?=$bt->button_js(t("Preview"), "ccm_previewInternalTheme(1, " . intval($t->getThemeID()) . ",'" . addslashes(str_replace(array("\r","\n",'\n'),'',$t->getThemeName())) . "')", "left");?>
			<?=$bt->button(t("Inspect"), $this->url('/dashboard/pages/themes/inspect', $t->getThemeID()), "left");?>
			<?=$bt->button(t("Customize"), $this->url('/dashboard/pages/themes/customize', $t->getThemeID()), "left");?>
			
			<?=$bt->button(t("Remove"), $this->url('/dashboard/pages/themes', 'remove', $t->getThemeID(), $valt->generate('remove')), "left");?>
			</td>
		</tr>
		<? }
	} ?>
	<? 
	if (count($tArray2) > 0) { ?>
	<tr>
		<td colspan="2" class="header"><br/><h2><?=t('Themes Available to Install')?></h2></td>
	</tr>

	<? foreach ($tArray2 as $t) { ?>
		<tr>
			<td><?=$t->getThemeThumbnail()?></td>
			<td class="ccm-template-content">
			<h3><?=$t->getThemeName()?></h3>
			<?=$t->getThemeDescription()?>
			<br/><br/>
			<?=$bt->button(t("Install"), $this->url('/dashboard/pages/themes','install', $t->getThemeHandle()), "left");?>
			
		</tr>
		<? }
	} ?>
	</table>
	</div>

	<? } ?>
	
	</div>
	
<? if (ENABLE_MARKETPLACE_SUPPORT == true) { ?>

<h1><span><?=t('More Themes')?></span></h1>
<div class="ccm-dashboard-inner">
<a href="<?=$this->url('/dashboard/install', 'browse', 'themes')?>"><?=t("Download more themes from the concrete5.org marketplace.")?></a>
</div>
<? } ?>
