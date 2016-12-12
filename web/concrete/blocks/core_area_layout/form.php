<?
	defined('C5_EXECUTE') or die("Access Denied.");
	use \Concrete\Core\Area\Layout\Preset as AreaLayoutPreset;
	$minColumns = 1;

	if ($controller->getTask() == 'add') {
		$spacing = 0;
		$iscustom = false;
	}
	$presets = Core::make('manager/area_layout_preset_provider')->getPresets();
?>

<ul id="ccm-layouts-toolbar" class="ccm-inline-toolbar ccm-ui">
	<li class="ccm-sub-toolbar-text-cell">
		<label for="useThemeGrid"><?=t("Grid:")?></label>
		<select name="gridType" id="gridType" style="width: auto !important">
			<optgroup label="<?=t('Grids')?>">
			<? if ($enableThemeGrid) { ?>
				<option value="TG"><?=$themeGridName?></option>
			<? } ?>
			<option value="FF"><?=t('Free-Form Layout')?></option>
			</optgroup>
			<? if (count($presets) > 0) { ?>
			<optgroup label="<?=t('Presets')?>">
			  	<? foreach($presets as $pr) { ?>
				    <option value="<?=$pr->getIdentifier()?>" <? if (is_object($selectedPreset) && $selectedPreset->getIdentifier() == $pr->getIdentifier()) { ?>selected<? } ?>><?=$pr->getName()?></option>
				<? } ?>
			</optgroup>
			<? } ?>
		</select>
	</li>
	<li data-grid-form-view="themegrid">
		<label for="themeGridColumns"><?=t("Columns:")?></label>
		<input type="text" name="themeGridColumns" id="themeGridColumns" style="width: 40px" <? if ($controller->getTask() == 'add') {?>  data-input="number" data-minimum="<?=$minColumns?>" data-maximum="<?=$themeGridMaxColumns?>" <? } ?> value="<?=$columnsNum?>" />
		<? if ($controller->getTask() == 'edit') {
			// we need this to actually go through the form in edit mode, for layout presets to be saveable in edit mode. ?>
			<input type="hidden" name="themeGridColumns" value="<?=$columnsNum?>" />
		<? } ?>
	</li>
	<li data-grid-form-view="custom" class="ccm-sub-toolbar-text-cell">
		<label for="columns"><?=t("Columns:")?></label>
		<input type="text" name="columns" id="columns" style="width: 40px" <? if ($controller->getTask() == 'add') {?> data-input="number" data-minimum="<?=$minColumns?>" data-maximum="<?=$maxColumns?>" <? } ?> value="<?=$columnsNum?>" />
		<? if ($controller->getTask() == 'edit') {
			// we need this to actually go through the form in edit mode, for layout presets to be saveable in edit mode. ?>
			<input type="hidden" name="columns" value="<?=$columnsNum?>" />
		<? } ?>
	</li>
	<li data-grid-form-view="custom">
		<label for="columns"><?=t("Spacing:")?></label>
		<input name="spacing" id="spacing" type="text" style="width: 40px" data-input="number" data-minimum="0" data-maximum="1000" value="<?=$spacing?>" />
	</li>
	<li data-grid-form-view="custom" class="ccm-inline-toolbar-icon-cell <? if (!$iscustom) { ?>ccm-inline-toolbar-icon-selected<? } ?>"><a href="#" data-layout-button="toggleautomated"><i class="fa fa-lock"></i></a>
		<input type="hidden" name="isautomated" value="<? if ($iscustom) { ?>0<? } else {?>1<? } ?>" />
	</li>
	<? if ($controller->getTask() == 'edit') {
		$bp = new Permissions($b); ?>

		<li class="ccm-inline-toolbar-icon-cell"><a href="#" data-layout-command="move-block"><i class="fa fa-arrows"></i></a></li>

		<?
		if ($bp->canDeleteBlock()) {
			$deleteMessage = t('Do you want to delete this layout? This will remove all blocks inside it.');
			?>
			<li class="ccm-inline-toolbar-icon-cell"><a href="#" data-menu-action="delete-layout"><i class="fa fa-trash-o"></i></a></li>
		<? } ?>
	<? } ?>

	<li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-cancel">
		<button id="ccm-layouts-cancel-button" type="button" class="btn btn-mini"><?=t("Cancel")?></button>
	</li>
	<li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save">
	  <button class="btn btn-primary" type="button" id="ccm-layouts-save-button"><? if ($controller->getTask() == 'add') { ?><?=t('Add Layout')?><? } else { ?><?=t('Update Layout')?><? } ?></button>
	</li>
</ul>

	<? if ($controller->getTask() == 'add') { ?>
		<input name="arLayoutMaxColumns" type="hidden" value="<?=$view->getAreaObject()->getAreaGridMaximumColumns()?>" />
	<? } ?>

<script type="text/javascript">
<?

if ($controller->getTask() == 'edit') {
	$editing = 'true';
} else {
	$editing = 'false';
}



?>

$(function() {


	<?
	if ($controller->getTask() == 'edit') { ?>
	$('#ccm-layouts-toolbar').on('click', 'a[data-menu-action=delete-layout]', function(e) {
		var editor = new Concrete.getEditMode(),
			area = editor.getAreaByID(<?=$a->getAreaID()?>),
			block = area.getBlockByID(<?=$b->getBlockID()?>);

		ConcreteEvent.subscribe('EditModeBlockDeleteComplete', function() {
			editor.destroyInlineEditModeToolbars();
			ConcreteEvent.unsubscribe('EditModeBlockDeleteComplete');
		});

		Concrete.event.fire('EditModeBlockDelete', {message: '<?=$deleteMessage?>', block: block, event: e});
		return false;
	});
	<? } ?>
	$('[data-input=number]').each(function() {
		var $spin = $(this);
		$(this).spinner({
			min: $spin.attr('data-minimum'),
			max: $spin.attr('data-maximum'),
			stop: function() {
				$spin.trigger('keyup');
			}
		});
	});

	$('#ccm-layouts-edit-mode').concreteLayout({
		'editing': <?=$editing?>,
		'supportsgrid': '<?=$enableThemeGrid?>',
		<? if ($enableThemeGrid) { ?>
        'containerstart':  '<?=addslashes($themeGridFramework->getPageThemeGridFrameworkContainerStartHTML())?>',
        'containerend': '<?=addslashes($themeGridFramework->getPageThemeGridFrameworkContainerEndHTML())?>',
		'rowstart':  '<?=addslashes($themeGridFramework->getPageThemeGridFrameworkRowStartHTML())?>',
		'rowend': '<?=addslashes($themeGridFramework->getPageThemeGridFrameworkRowEndHTML())?>',
        'additionalGridColumnClasses': '<?=$themeGridFramework->getPageThemeGridFrameworkColumnAdditionalClasses()?>',
        'additionalGridColumnOffsetClasses': '<?=$themeGridFramework->getPageThemeGridFrameworkColumnOffsetAdditionalClasses()?>',
		<? if ($controller->getTask() == 'add') { ?>
		'maxcolumns': '<?=$controller->getAreaObject()->getAreaGridMaximumColumns()?>',
		<? } else { ?>
		'maxcolumns': '<?=$themeGridMaxColumns?>',
		<? } ?>
		'gridColumnClasses': [
			<? $classes = $themeGridFramework->getPageThemeGridFrameworkColumnClasses();?>
			<? for ($i = 0; $i < count($classes); $i++) {
				$class = $classes[$i];?>
				'<?=$class?>' <? if (($i + 1) < count($classes)) { ?>, <? } ?>

			<? } ?>
		]
		<? } ?>
	});

	$('#ccm-layouts-toolbar').parent().concreteBlockInlineStyleCustomizer();

});


</script>

<div class="ccm-area-layout-control-bar-wrapper">
	<div id="ccm-area-layout-active-control-bar" class="ccm-area-layout-control-bar ccm-area-layout-control-bar-<?=$controller->getTask()?>"></div>
</div>
