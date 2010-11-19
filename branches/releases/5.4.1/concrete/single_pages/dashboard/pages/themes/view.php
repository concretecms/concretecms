<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$bt = Loader::helper('concrete/interface');
$valt = Loader::helper('validation/token');

$alreadyActiveMessage = t('This theme is currently active on your site.');

?>

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
			
			<?php echo $bt->button(t("Remove"), $this->url('/dashboard/pages/themes', 'remove', $t->getThemeID(), $valt->generate('remove')), "left");?>
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
	
<?php  if (ENABLE_MARKETPLACE_SUPPORT == true) { ?>

<h1><span><?php echo t('More Themes')?></span></h1>
<div class="ccm-dashboard-inner">
<a href="<?php echo $this->url('/dashboard/install', 'browse', 'themes')?>"><?php echo t("Download more themes from the concrete5.org marketplace.")?></a>
</div>
<?php  } ?>
