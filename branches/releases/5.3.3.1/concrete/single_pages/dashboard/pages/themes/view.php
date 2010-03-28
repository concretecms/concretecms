<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$bt = Loader::helper('concrete/interface');
$valt = Loader::helper('validation/token');

$alreadyActiveMessage = t('This theme is currently active on your site.');

?>

<script type="text/javascript">
ccm_isRemotelyLoggedIn = '<?php  echo UserInfo::isRemotelyLoggedIn()?>';
ccm_remoteUID = <?php  echo UserInfo::getRemoteAuthUserId() ?>;
ccm_remoteUName = '<?php  echo UserInfo::getRemoteAuthUserName()?>';
ccm_loginInstallSuccessFn = function() { str=unescape(window.location.pathname); window.location.href = str.replace(/\/-\/.*/, ''); };

$(document).ready(function(){
	ccmLoginHelper.bindInstallLinks();
});
</script>
<h1><span><?php echo t('Themes')?></span></h1>
	<div class="ccm-dashboard-inner">
	
	
	<?php  if (isset($activate_confirm)) { ?>
	<strong><?php echo t('Are you sure you wish to activate this theme? Any custom theme selections across your site will be reset.')?></strong>
	<br/><br/>
	<?php echo $bt->button(t("Yes, activate this theme."), $activate_confirm, "left");?>
	<?php echo t('or')?> <a href="<?php echo $this->url('/dashboard/pages/themes/')?>"><?php echo t('Cancel')?></a>
	
	<div class="ccm-spacer">&nbsp;</div>
	
	<?php  } else { ?>
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table border="0" cellspacing="0" cellpadding="0" id="ccm-template-list">
	<?php 
	if (count($tArray) == 0) { ?>
	<tr>
		<td colspan="5"><?php echo t('No themes are available.')?></td>
	</tr>
	<?php  } else {
		foreach ($tArray as $t) { ?>
		<tr <?php  if ($siteThemeID == $t->getThemeID()) { ?> class="ccm-theme-active" <?php  } ?>>
			<td><?php echo $t->getThemeThumbnail()?></td>
			<td class="ccm-template-content">
			<h2><?php echo $t->getThemeName()?></h2>
			<?php echo $t->getThemeDescription()?>
			<br/><br/>
			<?php  if ($siteThemeID == $t->getThemeID()) { ?>
				<?php echo $bt->button_js(t("Activate"), "alert('" . $alreadyActiveMessage . "')", "left", "ccm-button-inactive");?>
			<?php  } else { ?>
				<?php echo $bt->button(t("Activate"), $this->url('/dashboard/pages/themes','activate', $t->getThemeID()), "left");?>
			<?php  } ?>
			<?php echo $bt->button_js(t("Preview"), "ccm_previewInternalTheme(1, " . intval($t->getThemeID()) . ",'" . addslashes(str_replace(array("\r","\n",'\n'),'',$t->getThemeName())) . "')", "left");?>
			<?php echo $bt->button(t("Inspect"), $this->url('/dashboard/pages/themes/inspect', $t->getThemeID()), "left");?>
			<?php echo $bt->button(t("Customize"), $this->url('/dashboard/pages/themes/customize', $t->getThemeID()), "left");?>
			
			<?php  if ($t->isUninstallable()) { ?>
				<?php echo $bt->button(t("Remove"), $this->url('/dashboard/pages/themes', 'remove', $t->getThemeID(), $valt->generate('remove')), "left");?>
			<?php  } ?>
			</td>
		</tr>
		<?php  }
	} ?>
	<?php  
	if (count($tArray2) > 0) { ?>
	<tr>
		<td colspan="2" class="header"><br/><h2><?php echo t('Themes Available to Install')?></h2></td>
	</tr>

	<?php  foreach ($tArray2 as $t) { ?>
		<tr>
			<td><?php echo $t->getThemeThumbnail()?></td>
			<td class="ccm-template-content">
			<h3><?php echo $t->getThemeName()?></h3>
			<?php echo $t->getThemeDescription()?>
			<br/><br/>
			<?php echo $bt->button(t("Install"), $this->url('/dashboard/pages/themes','install', $t->getThemeHandle()), "left");?>
			
		</tr>
		<?php  }
	} ?>
	</table>
	</div>

	<?php  } ?>
	
	</div>
	
<?php  if (ENABLE_MARKETPLACE_SUPPORT == true && (!isset($activate_confirm))) { ?>
	<style>
	table#themesGrid td{ padding:8px 30px 15px 8px; text-align:center  } 
	table#themesGrid td .name{ font-weight:bold; margin-top:4px; font-size:14px; margin-left:20px; }
	table#themesGrid td .desc{ margin-bottom:4px; line-height: 16px; }
	</style>
	
	<h1><span><?php echo t('Get More Themes')?></span></h1>
	
	<div class="ccm-dashboard-inner">
		<div class="ccm-button-marketplace-install">
			
		<?php  if( !count($availableThemes) ){ ?>
			<div><?php echo t('Unable to connect to the marketplace.')?></div>
		<?php  }else{ ?>
			<table id="themesGrid" width="100%">
				<tr>
				<?php 
				$numCols=4;
				$colCount=0;
				foreach($availableThemes as $availableTheme){ 
					if($colCount==$numCols){
						echo '</tr><tr>';
						$colCount=0;
					}
					?>
					<td valign="top" width="<?php echo round(100/$numCols)?>%"> 
						<a href="<?php echo $availableTheme->getThemeURL() ?>" class="do-default" target="_blank"><img src="<?php echo $availableTheme->getThemeThumbnail() ?>" /></a>		
						<div class="name">
                        <a href="<?php echo $availableTheme->getThemeURL() ?>" class="do-default" target="_blank"><?php echo $availableTheme->getThemeName() ?></a>
						<a title="<?php echo t('Preview')?>" class="do-default preview"
                        	onclick="ccm_previewMarketplaceTheme(1, <?php echo intval($availableTheme->getRemoteCollectionID())?>,'<?php echo addslashes($availableTheme->getThemeName()) ?>','<?php echo addslashes($availableTheme->getThemeHandle()) ?>')" 
                            href="javascript:void(0)"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/magnifying.png" alt="<?php echo t('Preview')?>" /></a>
                           </div>
						<div class="desc"><?php echo $availableTheme->getThemeDescription() ?></div>
						<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED.'/package_install?type=theme&install=1&cID='.$availableTheme->getRemoteCollectionID()?>"><?php echo t('Install Theme')?> &raquo;</a>
					</td>
				<?php   $colCount++;
				}
				for($i=$colCount;$i<$numCols;$i++){
					echo '<td>&nbsp;</td>'; 
				} 
				?>
				</tr>
			</table>
		<?php  } ?>
		
		</div>
	</div>
<?php  } ?>
