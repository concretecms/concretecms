<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$bt = Loader::helper('concrete/interface');
?>
<h1><span><?php echo t('Themes')?></span></h1>
	<div class="ccm-dashboard-inner">
	
	
	<?php  if (isset($activate_confirm)) { ?>
	<strong><?php echo t('Are you sure you wish to activate this theme? Any custom theme selections across your site will be reset.')?></strong>
	<br/><br/>
	<?php echo $bt->button(t("Yes, activate this theme."), $activate_confirm, "left");?>
	<?php echo t('or')?> <a href="<?php echo $this->url('/dashboard/themes/')?>"><?php echo t('Cancel')?></a>
	
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
			<?php  if ($siteThemeID != $t->getThemeID()) { ?>
				<?php echo $bt->button(t("Activate"), $this->url('/dashboard/themes','activate', $t->getThemeID()), "left");?>
			<?php  } ?>
			<?php echo $bt->button(t("Inspect"), $this->url('/dashboard/themes/inspect', $t->getThemeID()), "left");?>
			
			<?php  if ($t->isUninstallable()) { ?>
				<?php echo $bt->button(t("Remove"), $this->url('/dashboard/themes', 'remove', $t->getThemeID()), "left");?>
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
			<?php echo $bt->button(t("Install"), $this->url('/dashboard/themes','install', $t->getThemeHandle()), "left");?>
			
		</tr>
		<?php  }
	} ?>
	</table>
	</div>

	<?php  } ?>
	
	</div>