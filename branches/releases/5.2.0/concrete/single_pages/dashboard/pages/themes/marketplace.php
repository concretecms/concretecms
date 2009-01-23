<style>
table#themesGrid td{ padding:8px 30px 15px 8px; text-align:center  } 
table#themesGrid td .name{ font-weight:bold; margin-top:4px; font-size:14px; margin-left:20px; }
table#themesGrid td .desc{ margin-bottom:4px; line-height: 16px; }
</style>

<h1><span><?php echo t('Available Themes')?></span></h1>

<div class="ccm-dashboard-inner">
		
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
					<a href="<?php echo $availableTheme->getThemeURL() ?>"><img src="<?php echo $availableTheme->getThemeThumbnail() ?>" /></a>		
					<div class="name"><a href="<?php echo $availableTheme->getThemeURL() ?>"><?php echo $availableTheme->getThemeName() ?></a>
					<a title="<?php echo t('Preview')?>" onclick="ccm_previewMarketplaceTheme(1, <?php echo intval($availableTheme->getRemoteCollectionID())?>,'<?php echo addslashes($availableTheme->getThemeName()) ?>','<?php echo addslashes($availableTheme->getThemeHandle()) ?>')" 
						href="javascript:void(0)" class="preview"><img src="<?php echo DIR_REL?>/concrete/images/icons/magnifying.png" alt="<?php echo t('Preview')?>" /></a></div>
					<div class="desc"><?php echo $availableTheme->getThemeDescription() ?></div>
					<a href="<?php echo $availableTheme->getThemeURL() ?>"><?php echo t('Get Theme')?> &raquo;</a>
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