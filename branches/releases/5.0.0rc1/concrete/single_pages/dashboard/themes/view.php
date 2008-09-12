<?php 
$bt = Loader::helper('concrete/interface');
?>
<h1><span>Themes</span></h1>
	<div class="ccm-dashboard-inner">
	
	
	<?php  if (isset($activate_confirm)) { ?>
	<strong>Are you sure you wish to activate this theme? Any custom theme selections across your site will be reset.</strong>
	<br/><br/>
	<?php echo $bt->button("Yes, activate this theme.", $activate_confirm, "left");?>
	or <a href="<?php echo $this->url('/dashboard/themes/')?>">Cancel</a>
	
	<div class="ccm-spacer">&nbsp;</div>
	
	<?php  } else { ?>
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table border="0" cellspacing="0" cellpadding="0" id="ccm-template-list">
	<?php 
	if (count($tArray) == 0) { ?>
	<tr>
		<td colspan="5">No themes are available.</td>
	</tr>
	<?php  } else {
		foreach ($tArray as $t) { ?>
		<tr <?php  if ($siteTheme->getThemeID() == $t->getThemeID()) { ?> class="ccm-theme-active" <?php  } ?>>
			<td><?php echo $t->getThemeThumbnail()?></td>
			<td class="ccm-template-content">
			<h2><?php echo $t->getThemeName()?></h2>
			<?php echo $t->getThemeDescription()?>
			<br/><br/>
			<?php echo $bt->button("Activate", $this->url('/dashboard/themes','activate', $t->getThemeID()), "left");?>
			<?php echo $bt->button("Inspect", $this->url('/dashboard/themes/inspect', $t->getThemeID()), "left");?>
			
			<?php  if ($t->isUninstallable()) { ?>
				<?php echo $bt->button("Remove", $this->url('/dashboard/themes/inspect', 'remove', $t->getThemeID()), "left");?>
			<?php  } ?>
			</td>
		</tr>
		<?php  }
	} ?>
	<?php  
	if (count($tArray2) > 0) { ?>
	<tr>
		<td colspan="2" class="header"><br/><h2>Themes Available to Install</h2></td>
	</tr>

	<?php  foreach ($tArray2 as $t) { ?>
		<tr>
			<td><?php echo $t->getThemeThumbnail()?></td>
			<td class="ccm-template-content">
			<h3><?php echo $t->getThemeName()?></h3>
			<?php echo $t->getThemeDescription()?>
			<br/><br/>
			<?php echo $bt->button("Install", $this->url('/dashboard/themes','install', $t->getThemeHandle()), "left");?>
			
		</tr>
		<?php  }
	} ?>
	</table>
	</div>

	<?php  } ?>
	
	</div>