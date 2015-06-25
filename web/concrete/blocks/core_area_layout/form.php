<?
	defined('C5_EXECUTE') or die("Access Denied.");
	use \Concrete\Core\Area\Layout\Preset as AreaLayoutPreset;
	$minColumns = 1;

	$backgroundColor = '';
	$backgroundImage = false;
	$backgroundRepeat = 'no-repeat';
	$customClass = null;
	$paddingTop = '';
	$paddingLeft = '';
	$paddingRight = '';
	$paddingBottom = '';
	$sliderMin = \Config::get('concrete.limits.style_customizer.size_min', -50);
	$sliderMax = \Config::get('concrete.limits.style_customizer.size_max', 200);
	if ($controller->getTask() == 'add') {
		$spacing = 0;
		$iscustom = false;
	} else {
		$style = $b->getCustomStyle();
		if (is_object($style)) {
			$styleSet = $style->getStyleSet();
			$customClass = $styleSet->getCustomClass();
			$backgroundColor = $styleSet->getBackgroundColor();
			$backgroundImage = $styleSet->getBackgroundImageFileObject();
			$backgroundRepeat = $styleSet->getBackgroundRepeat();
			$paddingTop = $styleSet->getPaddingTop();
			$paddingLeft = $styleSet->getPaddingLeft();
			$paddingRight = $styleSet->getPaddingRight();
			$paddingBottom = $styleSet->getPaddingBottom();
		}
	}
	$c = Page::getCurrentPage();
	$presets = Core::make('manager/area_layout_preset_provider')->getPresets();
	$pt = $c->getCollectionThemeObject();

	$customClasses = array();
	if (is_object($pt)) {
		$areaClasses = $pt->getThemeBlockClasses();
		if (isset($areaClasses[BLOCK_HANDLE_LAYOUT_PROXY])) {
			$customClasses = $areaClasses[BLOCK_HANDLE_LAYOUT_PROXY];
		}
	}

	$repeatOptions = array(
		'no-repeat' => t('No Repeat'),
		'repeat-x' => t('Horizontally'),
		'repeat-y' => t('Vertically'),
		'repeat' => t('Horizontally & Vertically')
	);

	$customClassesSelect = array('' => t('None'));

	if (is_array($customClasses)) {
		foreach($customClasses as $class) {
			$customClassesSelect[$class] = $class;
		}
	}


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
	<li class="ccm-inline-toolbar-icon-cell"><a href="#" data-toggle="dropdown" title="<?=t('Background Color and Image')?>"><i class="fa fa-image"></i></a>

		<div class="ccm-inline-design-dropdown-menu ccm-inline-design-dropdown-menu-doubled dropdown-menu">

			<div class="row">
				<div class="col-sm-6">
					<h3><?=t('Background')?></h3>
					<div>
						<?=t('Color')?>
						<?=Loader::helper('form/color')->output('backgroundColor', $backgroundColor);?>
					</div>
					<hr />
					<div>
						<?=t('Image')?>
						<?=Core::make('helper/concrete/asset_library')->image('backgroundImageFileID', 'backgroundImageFileID', t('Choose Image'), $backgroundImage);?>
					</div>
					<div class="ccm-inline-select-container">
						<?=t('Repeats')?>
						<?=Core::make('helper/form')->select('backgroundRepeat', $repeatOptions, $backgroundRepeat);?>
					</div>
					<? if (count($customClassesSelect)) { ?>
						<hr/>
						<div>
							<?=t('Custom Class')?>
							<?= $form->select('customClass', $customClassesSelect, $customClass);?>
						</div>
					<? } ?>
				</div>
				<div class="col-sm-6">
					<h3><?=t('Padding')?></h3>
					<div>
						<span class="ccm-inline-style-slider-heading"><?=t('Top')?></span>
						<div class="ccm-inline-style-sliders" data-style-slider-min="<?= $sliderMin ?>" data-style-slider-max="<?= $sliderMax ?>" data-style-slider-default-setting="0"></div>
                <span class="ccm-inline-style-slider-display-value">
                    <input type="text" name="paddingTop" id="paddingTop" data-value-format="px" class="ccm-inline-style-slider-value" value="<?php echo $paddingTop ? $paddingTop : '0px' ?>" <?php echo $paddingTop ? '' : 'disabled' ?> autocomplete="off" />
                </span>
					</div>
					<div>
						<span class="ccm-inline-style-slider-heading"><?=t('Right')?></span>
						<div class="ccm-inline-style-sliders" data-style-slider-min="<?= $sliderMin ?>" data-style-slider-max="<?= $sliderMax ?>" data-style-slider-default-setting="0"></div>
                <span class="ccm-inline-style-slider-display-value">
                    <input type="text" name="paddingRight" id="paddingRight" data-value-format="px" class="ccm-inline-style-slider-value" value="<?php echo $paddingRight ? $paddingRight : '0px' ?>" <?php echo $paddingRight ? '' : 'disabled' ?> autocomplete="off" />
                </span>
					</div>
					<div>
						<span class="ccm-inline-style-slider-heading"><?=t('Bottom')?></span>
						<div class="ccm-inline-style-sliders" data-style-slider-min="<?= $sliderMin ?>" data-style-slider-max="<?= $sliderMax ?>" data-style-slider-default-setting="0"></div>
                <span class="ccm-inline-style-slider-display-value">
                    <input type="text" name="paddingBottom" id="paddingBottom" data-value-format="px" class="ccm-inline-style-slider-value" value="<?php echo $paddingBottom ? $paddingBottom : '0px' ?>" <?php echo $paddingBottom ? '' : 'disabled' ?> autocomplete="off" />
                </span>
					</div>
					<div>
						<span class="ccm-inline-style-slider-heading"><?=t('Left')?></span>
						<div class="ccm-inline-style-sliders" data-style-slider-min="<?= $sliderMin ?>" data-style-slider-max="<?= $sliderMax ?>" data-style-slider-default-setting="0"></div>
               <span class="ccm-inline-style-slider-display-value">
                <input type="text" name="paddingLeft" id="paddingLeft" data-value-format="px" class="ccm-inline-style-slider-value" value="<?php echo $paddingLeft ? $paddingLeft : '0px' ?>" <?php echo $paddingLeft ? '' : 'disabled' ?> autocomplete="off" />
            </span>
					</div>
				</div>
			</div>
		</div>

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

	reloadAreaLayoutStyles = function(block) {
		var url = CCM_DISPATCHER_FILENAME + '/ccm/system/block/core_area_layout/get_style_set_data/';
		$.ajax({
			url: url,
			dataType: 'json',
			type: 'post',
			data: {
				'bID': block.getId(),
				'cID': block.getCID(),
				'arHandle': <?=json_encode($a->getAreaHandle())?>
			},
			success: function(r) {
				$('style[data-style-set][data-block-style-block-id=' + block.getId() + ']').remove();
				if (r.style) {
					$('head').append(r.style);
				}

			}
		});
	}

	ConcreteEvent.unbind('EditModeAddBlockComplete.coreAreaLayout');
	ConcreteEvent.unbind('EditModeUpdateBlockComplete.coreAreaLayout');

	ConcreteEvent.on('EditModeUpdateBlockComplete.coreAreaLayout', function(e, data) {
		reloadAreaLayoutStyles(data.block);
	});


	ConcreteEvent.on('EditModeAddBlockComplete.coreAreaLayout', function(e, data) {
		reloadAreaLayoutStyles(data.block);
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
