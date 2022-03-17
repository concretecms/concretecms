<?php
    defined('C5_EXECUTE') or die('Access Denied.');
    $minColumns = 1;
    $columnsNum = $columnsNum ?? 1;
    $maxColumns = $maxColumns ?? 12;
    $enableThemeGrid = $enableThemeGrid ?? false;


    /** @var \Concrete\Block\CoreAreaLayout\Controller $controller */
    /** @var \Concrete\Core\Block\Block|null $b */
    $b = $b ?? null;
    /** @var \Concrete\Core\Block\View\BlockView $view */
    /** @var \Concrete\Core\Area\Area|null $a */
    $a = $a ?? null;
    /** @var \Concrete\Core\Page\Theme\GridFramework\GridFramework $themeGridFramework */
    if ($controller->getAction() === 'add') {
        $spacing = 0;
        $iscustom = false;
    }
    $presets = app('manager/area_layout_preset_provider')->getPresets();
?>

<ul id="ccm-layouts-toolbar" class="ccm-inline-toolbar ccm-ui">
	<li class="ccm-sub-toolbar-text-cell">
		<label for="useThemeGrid"><?=t('Grid:')?></label>
		<select name="gridType" id="gridType" style="width: auto !important">
			<optgroup label="<?=t('Grids')?>">
			<?php if ($enableThemeGrid) {
    ?>
				<option value="TG"><?=$themeGridName ?? ''?></option>
			<?php
} ?>
			<option value="FF"><?=t('Free-Form Layout')?></option>
			</optgroup>
			<?php if (count($presets) > 0) {
    ?>
			<optgroup label="<?=t('Presets')?>">
			  	<?php foreach ($presets as $pr) {
    ?>
				    <option value="<?=$pr->getIdentifier()?>" <?php if (isset($selectedPreset) && is_object($selectedPreset) && $selectedPreset->getIdentifier() == $pr->getIdentifier()) {
    ?>selected<?php
}
    ?>><?=$pr->getName()?></option>
				<?php
}
    ?>
			</optgroup>
			<?php
} ?>
		</select>
	</li>
	<li data-grid-form-view="themegrid">
		<label for="themeGridColumns"><?=t('Columns:')?></label>
		<input type="number" name="themeGridColumns" id="themeGridColumns" style="width: 40px" <?php if ($controller->getAction() === 'add') {
    ?>  min="<?=$minColumns?>" max="<?= $themeGridMaxColumns ?? '' ?>" <?php
} ?> value="<?=$columnsNum?>" />
		<?php if ($controller->getAction() === 'edit') {
    // we need this to actually go through the form in edit mode, for layout presets to be saveable in edit mode.?>
			<input type="hidden" name="themeGridColumns" value="<?=$columnsNum?>" />
		<?php
} ?>
	</li>
	<li data-grid-form-view="custom" class="ccm-sub-toolbar-text-cell">
		<label for="columns"><?=t('Columns:')?></label>
		<input type="number" name="columns" id="columns" style="width: 40px" <?php if ($controller->getAction() === 'add') {
    ?> min="<?=$minColumns?>" max="<?=$maxColumns?>" <?php
} ?> value="<?=$columnsNum?>" />
		<?php if ($controller->getAction() === 'edit') {
    // we need this to actually go through the form in edit mode, for layout presets to be saveable in edit mode.?>
			<input type="hidden" name="columns" value="<?=$columnsNum?>" />
		<?php
} ?>
	</li>
	<li data-grid-form-view="custom">
		<label for="columns"><?=t('Spacing:')?></label>
		<input name="spacing" id="spacing" type="number" style="width: 40px" min="0" max="1000" value="<?=$spacing ?? ''?>" />
	</li>
	<li data-grid-form-view="custom" class="ccm-inline-toolbar-icon-cell <?php if (empty($iscustom)) {
    ?>ccm-inline-toolbar-icon-selected<?php
    } ?>"><button type="button" id="ccm-layouts-toggle-automated-button" data-layout-button="toggleautomated" class="btn-sm btn" title="<?=h(t('Toggle Custom Widths.'));?>"><i class="fas fa-lock"></i></button>
		<input type="hidden" name="isautomated" value="<?=empty($iscustom)? 1 :0 ?>" />
	</li>
	<?php if ($controller->getAction() === 'edit') {
    $bp = new \Concrete\Core\Permission\Checker($b);
    ?>
        <li class="ccm-inline-toolbar-icon-cell">
            <button id="ccm-layouts-move-block-button" type="button" data-layout-command="move-block" class="btn-sm btn btn-outline-primary" data-placement="top" data-custom-class="light-tooltip" title="<?=h(t("Move Block"))?>">
                <i class="fas fa-arrows-alt"></i>
            </button>
        </li>

		<?php
        /** @phpstan-ignore-next-line */
        if ($bp->canDeleteBlock()) {
            $deleteMessage = t('Do you want to delete this layout? This will remove all blocks inside it.');
            ?>
            <li class="ccm-inline-toolbar-icon-cell">
                <button id="ccm-layouts-delete-button" type="button" class="btn-sm btn btn-outline-danger" data-placement="top" data-custom-class="light-tooltip" title="<?=h(t("Delete Block"))?>">
                    <i class="fas fa-trash"></i>
                </button>
            </li>
		<?php
        }
    ?>
	<?php
} ?>

	<li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-cancel">
		<button id="ccm-layouts-cancel-button" @click="cancelDesign" type="button" class="btn btn-mini"><?=t('Cancel')?></button>
	</li>
	<li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save">
	  <button class="btn btn-primary" type="button" @click="saveDesign" id="ccm-layouts-save-button"><?php if ($controller->getAction() === 'add') {
    ?><?=t('Add Layout')?><?php
} else {
    ?><?=t('Update Layout')?><?php
} ?></button>
	</li>
</ul>

	<?php if ($controller->getAction() === 'add') {
    ?>
		<input name="arLayoutMaxColumns" type="hidden" value="<?=$view->getAreaObject()->getAreaGridMaximumColumns()?>" />
	<?php
} ?>

<script type="text/javascript">
<?php

if ($controller->getAction() === 'edit') {
    $editing = 'true';
} else {
    $editing = 'false';
}

?>

$(function() {
    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: $('#ccm-layouts-toolbar').parent()[0],
            components: config.components,
            data: {
                areaId: <?=is_object($a) ? $a->getAreaID() : 0?>,
                blockId: <?=is_object($b) ? $b->getBlockID() : 0?>,
            },
            methods: {
                <?php
                if ($controller->getAction() === 'edit') {
                ?>
                deleteBlock(e) {
                    var editor = new Concrete.getEditMode(),
                        area = editor.getAreaByID(this.areaId),
                        block = area.getBlockByID(this.blockId);
                    ConcreteEvent.subscribe('EditModeBlockDeleteAfterComplete', function() {
                        editor.destroyInlineEditModeToolbars();
                        ConcreteEvent.unsubscribe('EditModeBlockDeleteAfterComplete');
                    });

                    Concrete.event.fire('EditModeBlockDelete', {message: '<?=$deleteMessage ?? ''?>', block: block, event: e});

                },
                <?php } ?>
                cancelDesign(){
                    $('#ccm-inline-toolbar-container').hide();
                    ConcreteEvent.fire('EditModeExitInline');
                },
                saveDesign(){
                    const form = $('#ccm-inline-design-form');
                    form.concreteAjaxForm({
                        success:(resp) => {
                            var editor = new Concrete.getEditMode()
                            var area = editor.getAreaByID(resp.aID)
                            var block = area.getBlockByID(parseInt(resp.originalBlockID))
                            var arEnableGridContainer = area.getEnableGridContainer() ? 1 : 0
                            var action = CCM_DISPATCHER_FILENAME + '/ccm/system/block/render';
                            const request = {
                                arHandle: area.getHandle(),
                                cID: resp.cID,
                                bID: resp.bID,
                                arEnableGridContainer: arEnableGridContainer
                            };
                            $.get(action, request, (r)=> {
                                ConcreteToolbar.disableDirectExit()
                                var newBlock = block.replace(r)
                                ConcreteAlert.notify({
                                    message: resp.message
                                })

                                this.refreshStyles(resp)
                                ConcreteEvent.fire('EditModeExitInline', {
                                    action: 'save_inline',
                                    block: newBlock
                                })
                                ConcreteEvent.fire('EditModeExitInlineComplete', {
                                    block: newBlock
                                })
                                $.fn.dialog.hideLoader()
                                editor.destroyInlineEditModeToolbars()
                                editor.scanBlocks()
                            })
                        },
                        error:(r) => {
                            $(this.$el).prependTo('#ccm-inline-toolbar-container').show()
                        }
                    })
                    $(this.$el).hide().prependTo(form);
                    form.submit();
                    ConcreteEvent.unsubscribe('EditModeExitInlineComplete');
                },
                handleBlockMove(event, data)
                {

                    this.blockId = data.block.getId();
                    this.areaId = data.area.getId();
                    // Rebind peper after move
                    this.bindPeper()

                },
                bindPeper() {
                    const peper = $('[data-layout-command="move-block"]');
                    let editor = new Concrete.getEditMode(),
                        area = editor.getAreaByID(this.areaId),
                        block = area.getBlockByID(this.blockId);
                    $.pep.unbind(peper);
                    peper.pep(block.getPepSettings());
                }

            },
            mounted() {
                $('#ccm-layouts-edit-mode').concreteLayout({
                    'editing': <?=$editing?>,
                    'supportsgrid': '<?=$enableThemeGrid?>',
                    <?php if ($enableThemeGrid) {
                    ?>
                    'containerstart':  '<?=addslashes($themeGridFramework->getPageThemeGridFrameworkContainerStartHTML())?>',
                    'containerend': '<?=addslashes($themeGridFramework->getPageThemeGridFrameworkContainerEndHTML())?>',
                    'rowstart':  '<?=addslashes($themeGridFramework->getPageThemeGridFrameworkRowStartHTML())?>',
                    'rowend': '<?=addslashes($themeGridFramework->getPageThemeGridFrameworkRowEndHTML())?>',
                    'additionalGridColumnClasses': '<?=$themeGridFramework->getPageThemeGridFrameworkColumnAdditionalClasses()?>',
                    'additionalGridColumnOffsetClasses': '<?=$themeGridFramework->getPageThemeGridFrameworkColumnOffsetAdditionalClasses()?>',
                    <?php if ($controller->getAction() === 'add') {
                    ?>
                    'maxcolumns': '<?=$controller->getAreaObject()->getAreaGridMaximumColumns()?>',
                    <?php
                    } else {
                    ?>
                    'maxcolumns': '<?=$themeGridMaxColumns ?? ''?>',
                    <?php
                    }
                    ?>
                    'gridColumnClasses': [
                        <?php $classes = $themeGridFramework->getPageThemeGridFrameworkColumnClasses();
                        ?>
                        <?php for ($i = 0,$iMax = count($classes); $i < $iMax; $i++) {
                        $class = $classes[$i];
                        ?>
                        '<?=$class?>' <?php if (($i + 1) < $iMax) {
                        ?>, <?php
                        }
                        ?>

                        <?php
                        }
                        ?>
                    ]
                    <?php
                    } ?>
                });
                new bootstrap.Tooltip(document.querySelector('button#ccm-layouts-delete-button'), { customClass: 'light-tooltip' });
                new bootstrap.Tooltip(document.querySelector('button#ccm-layouts-move-block-button'), { customClass: 'light-tooltip' });
                new bootstrap.Tooltip(document.querySelector('button#ccm-layouts-toggle-automated-button'), { customClass: 'light-tooltip' });
                <?php if ($controller->getAction() === 'edit') {
                    ?>
                const peper = $('[data-layout-command="move-block"]');
                this.bindPeper()
                ConcreteEvent.unsubscribe('EditModeBlockMoveComplete');
                ConcreteEvent.on('EditModeBlockMoveComplete', this.handleBlockMove);

                $('button#ccm-layouts-delete-button').on('click',(e)=>{
                    this.deleteBlock(e)
                })
                <?php
                } ?>

            },
            destroy() {
                ConcreteEvent.unsubscribe('EditModeBlockMoveComplete');
            }
        });

    });


});

</script>

<div class="ccm-area-layout-control-bar-wrapper">
	<div id="ccm-area-layout-active-control-bar" class="ccm-area-layout-control-bar ccm-area-layout-control-bar-<?=$controller->getAction()?>"></div>
</div>
