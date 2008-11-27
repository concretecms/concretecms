<style>
table#themesGrid td{ padding:8px; text-align:center  } 
table#themesGrid td .name{ font-weight:bold; margin-top:4px; font-size:14px; margin-left:20px; }
table#themesGrid td .desc{ margin-bottom:4px}
</style>

<script>
function previewMarketplaceTheme(themeCID,themeName){
	$.fn.dialog.open({
		title: themeName,
		href: "<?=REL_DIR_FILES_TOOLS_REQUIRED?>/preview_external_theme.php?cID="+themeCID,
		width: '85%',
		modal: false,
		height: '80%'
	});	
}
</script>

<h1><span><?=t('Available Themes')?></span></h1>

<div class="ccm-dashboard-inner">
		
	<? if( !count($availableThemes) ){ ?>
		<div>No themes found.</div>
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
				<td width="<?=round(100/$numCols)?>%"> 
					<a href="<?=$availableTheme->getThemeURL() ?>"><img src="<?=$availableTheme->getThemeThumbnail() ?>" /></a>			
					<div style="float:right; width:20px; padding-top:6px;">
						<a onclick="previewMarketplaceTheme(<?=$availableTheme->getMarketPlaceCID()?>,'<?=addslashes($availableTheme->getThemeName()) ?>')" 
						href="javascript:void(0)" class="preview"><img src="<?=DIR_REL?>/concrete/images/icons/magnifying.png" /></a>
					</div>
					<div class="name"><a href="<?=$availableTheme->getThemeURL() ?>"><?=$availableTheme->getThemeName() ?></a></div>
					<div class="desc"><?=$availableTheme->getThemeDescription() ?></div>
					<a href="<?=$availableTheme->getThemeURL() ?>">Get Theme &raquo;</a>
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