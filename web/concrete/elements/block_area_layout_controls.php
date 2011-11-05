<div id="ccm-layout-controls-<?=intval($layout->cvalID) ?>" class="ccm-layout-controls ccm-layout-controls-layoutID-<?=intval($layout->layoutID) ?>">  


	<input class="layout_column_count" name="layout_column_count" type="hidden" value="<?=intval($layout->columns) ?>" /> 
	
	<input id="layout_col_break_points_<?=intval($layout->cvalID) ?>" class="layout_col_break_points" name="layout_col_break_points" type="hidden" value="<?= htmlspecialchars(join('|',$layout->breakpoints )) ?>" />
	
	<input class="layout_locked" name="layout_locked" type="hidden" value="<?= intval($layout->locked) ?>" />
	
	<div class="ccm-layout-menu-button">
	
	</div>
	<div id="ccm-layout-controls-slider-<?=intval($layout->cvalID) ?>" class="ccm-layout-controls-slider ccm-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"></div>

	<script type="text/javascript">
	
	$(function(){ 
		<? if( intval($layout->cvalID) ){ ?>var<? } ?> ccmLayout<?=intval($layout->cvalID) ?> = new ccmLayout( <?=intval($layout->cvalID) ?>, <?=intval($layout->layoutID) ?>, "<?= $layout->getAreaHandle() ?>", <?=intval($layout->locked) ?> );
		ccmLayout<?=intval($layout->cvalID) ?>.init(); 
		
	});
	</script>
	
</div>