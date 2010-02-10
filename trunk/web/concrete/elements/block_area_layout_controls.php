<div id="ccm-layout-controls-<?=intval($layout->layoutID) ?>" class="ccm-layout-controls">  


	<input class="layout_column_count" name="layout_column_count" type="hidden" value="<?=intval($layout->columns) ?>" /> 
	
	<input class="layout_col_break_points" name="layout_col_break_points" type="hidden" value="<?= htmlspecialchars(join('|',$layout->breakpoints )) ?>" />
	
	<input class="layout_locked" name="layout_locked" type="hidden" value="<?= intval($layout->locked) ?>" />
	
	<div class="ccm-layout-menu-button">
	
	</div>
	<div id="ccm-layout-controls-slider-<?=intval($layout->layoutID) ?>" class="ccm-layout-controls-slider ccm-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"></div>

	<script>
	<? if( intval($layout->layoutID) ){ ?>var<? } ?> ccmLayout<?=intval($layout->layoutID) ?> = new ccmLayout( <?=intval($layout->layoutID) ?>, "<?= $layout->getAreaHandle() ?>", <?=intval($layout->locked) ?> );
	$(function(){  ccmLayout<?=intval($layout->layoutID) ?>.init(); });
	</script>
	
</div>