	<h1><span>Themes</span></1>
	<div class="ccm-dashboard-inner">
	
	
	<? if (isset($activate_confirm)) { ?>
	<strong>Are you sure you wish to activate this theme? Any custom theme selections across your site will be reset.</strong>
	<br/><br/>
	<input type="button" onclick="location.href='<?=$activate_confirm?>'" value="Yes, Activate this Theme &gt;" />
	or <a href="<?=$this->url('/dashboard/themes/')?>">Cancel</a>
	<? } else { ?>
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" id="ccm-template-list">
	<?
	if (count($tArray) == 0) { ?>
	<tr>
		<td colspan="5">No themes are available.</td>
	</tr>
	<? } else {
		foreach ($tArray as $t) { ?>
		<tr>
			<td><?=$t->getThemeThumbnail()?></td>
			<td><strong><?=$t->getThemeHandle()?></strong> <? if ($siteTheme == $t->getThemeID()) { ?>Active Theme<? } ?><br/>
			<?=$t->getThemeName()?><br/>
			<?=$t->getThemeDescription()?>
			<br/><br/>
			
			<input type="button" value="Activate" onclick="location.href='<?=$this->url('/dashboard/themes','activate', $t->getThemeID())?>'" />
			<input type="button" value="Inspect" onclick="location.href='<?=$this->url('/dashboard/themes/inspect', $t->getThemeID())?>'" />
			<? if ($t->isUninstallable()) { ?>
				<input type="button" value="Remove" onclick="location.href='<?=$this->url('/dashboard/themes','remove', $t->getThemeID())?>'" />
			<? } ?>
			</td>
		</tr>
		<? }
	} ?>
	<? 
	if (count($tArray2) > 0) { ?>
	<tr>
		<td colspan="2" class="header"><br/><h2>Themes Available to Install</h2></td>
	</tr>

	<? foreach ($tArray2 as $t) { ?>
		<tr>
			<td><?=$t->getThemeThumbnail()?></td>
			<td><strong><?=$t->getThemeHandle()?></strong> <? if ($siteTheme == $t->getThemeID()) { ?>Active Theme<? } ?><br/>
			<?=$t->getThemeName()?><br/>
			<?=$t->getThemeDescription()?>
			<br/><br/>
			
				<input type="button" value="Install" onclick="location.href='<?=$this->url('/dashboard/themes','install', $t->getThemeHandle())?>'" />

		</tr>
		<? }
	} ?>
	</table>
	</div>

	<? } ?>
	
	</div>