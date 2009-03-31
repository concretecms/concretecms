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
			
			<? if ($t->isUninstallable()) { ?>
				<?=$bt->button(t("Remove"), $this->url('/dashboard/pages/themes', 'remove', $t->getThemeID(), $valt->generate('remove')), "left");?>
			<? } ?>
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
	
<? if (ENABLE_MARKETPLACE_SUPPORT == true && (!isset($activate_confirm))) { ?>
	<style>
	table#themesGrid td{ padding:8px 30px 15px 8px; text-align:center  } 
	table#themesGrid td .name{ font-weight:bold; margin-top:4px; font-size:14px; margin-left:20px; }
	table#themesGrid td .desc{ margin-bottom:4px; line-height: 16px; }
	</style>
	
	<h1><span><?=t('Get More Themes')?></span></h1>
	
	<div class="ccm-dashboard-inner">
			
		<? if( !count($availableThemes) ){ ?>
			<div><?=t('Unable to connect to the marketplace.')?></div>
		<? }else{ ?>
			<table id="themesGrid" width="100%">
				<tr>
				<?
				$numCols=4;
				$colCount=0;
				foreach($availableThemes as $availableTheme){ 
					if($colCount==$numCols){
						echo '</tr><tr>';
						$colCount=0;
					}
					?>
					<td valign="top" width="<?=round(100/$numCols)?>%"> 
						<a href="<?=$availableTheme->getThemeURL() ?>"><img src="<?=$availableTheme->getThemeThumbnail() ?>" /></a>		
						<div class="name"><a href="<?=$availableTheme->getThemeURL() ?>"><?=$availableTheme->getThemeName() ?></a>
						<a title="<?=t('Preview')?>" onclick="ccm_previewMarketplaceTheme(1, <?=intval($availableTheme->getRemoteCollectionID())?>,'<?=addslashes($availableTheme->getThemeName()) ?>','<?=addslashes($availableTheme->getThemeHandle()) ?>')" 
							href="javascript:void(0)" class="preview"><img src="<?=ASSETS_URL_IMAGES?>/icons/magnifying.png" alt="<?=t('Preview')?>" /></a></div>
						<div class="desc"><?=$availableTheme->getThemeDescription() ?></div>
						<a href="<?=$this->url('/dashboard/pages/themes','download_remote', $availableTheme->getRemoteCollectionID(), 1) ?>"><?=t('Install Theme')?> &raquo;</a>
					</td>
				<?  $colCount++;
				}
				for($i=$colCount;$i<$numCols;$i++){
					echo '<td>&nbsp;</td>'; 
				} 
				?>
				</tr>
			</table>
		<? } ?>
		
	</div>
<? } ?>
