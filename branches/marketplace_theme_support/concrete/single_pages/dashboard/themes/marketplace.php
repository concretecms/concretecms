<style>
table#themesGrid td{ padding:8px; text-align:center  } 
table#themesGrid td .name{ font-weight:bold; margin-top:4px; font-size:14px; }
table#themesGrid td .desc{ margin-bottom:4px}
</style>

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