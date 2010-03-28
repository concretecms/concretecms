<div id="ccm-layout-controls-<?php echo intval($layout->cvalID) ?>" class="ccm-layout-controls ccm-layout-controls-layoutID-<?php echo intval($layout->layoutID) ?>">  


	<input class="layout_column_count" name="layout_column_count" type="hidden" value="<?php echo intval($layout->columns) ?>" /> 
	
	<input id="layout_col_break_points_<?php echo intval($layout->cvalID) ?>" class="layout_col_break_points" name="layout_col_break_points" type="hidden" value="<?php echo  htmlspecialchars(join('|',$layout->breakpoints )) ?>" />
	
	<input class="layout_locked" name="layout_locked" type="hidden" value="<?php echo  intval($layout->locked) ?>" />
	
	<div class="ccm-layout-menu-button">
	
	</div>
	<div id="ccm-layout-controls-slider-<?php echo intval($layout->cvalID) ?>" class="ccm-layout-controls-slider ccm-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"></div>

	<script>
	<?php  if( intval($layout->cvalID) ){ ?>var<?php  } ?> ccmLayout<?php echo intval($layout->cvalID) ?> = new ccmLayout( <?php echo intval($layout->cvalID) ?>, <?php echo intval($layout->layoutID) ?>, "<?php echo  $layout->getAreaHandle() ?>", <?php echo intval($layout->locked) ?> );
	$(function(){  ccmLayout<?php echo intval($layout->cvalID) ?>.init(); });
	</script>
	
</div>