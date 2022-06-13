<?php if ($style instanceof \Concrete\Core\Block\CustomStyle) {
    $areaID = $style->block->getBlockAreaObject()->getAreaID();
    $blockID = $style->block->getBlockID();
    $pageId = $page->getCollectionID();
    ?>

<form method="post" action="<?=$saveAction?>" id="ccm-inline-design-form">
    <ul class="ccm-inline-toolbar ccm-ui">
        <li class="ccm-inline-toolbar-select">
            <select id="bFilename" name="bFilename" class="form-select form-select-sm" v-model="bFilename" @change="refreshTemplate">
                <option value="">(<?=t('None selected')?>)</option>
                <?php
                foreach ($templates as $tpl) { ?>
                    <option value="<?=$tpl->getTemplateFileFilename()?>" <?php if ($bFilename == $tpl->getTemplateFileFilename()) { ?> selected <?php } ?>><?=$tpl->getTemplateFileDisplayName()?></option>
                <?php } ?>
            </select>
        </li>
        <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-cancel">
            <button data-action="cancel-design" type="button" @click="cancelDesign" class="btn btn-mini"><?=t("Cancel")?></button>
        </li>
        <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save">
            <button data-action="save-design" class="btn btn-primary" @click="saveDesign" type="button"><?=t('Save')?></button>
        </li>
    </ul>
</form>
<script>
    $(function() {
        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: 'ul.ccm-inline-toolbar',
                components: config.components,
                data: {
                    bFilename: '<?=$bFilename ?? null?>',
                    pageId: <?=(int) $pageId?>,
                    blockId: <?=(int) $blockID?>,
                    areaId: <?=(int) $areaID?>,
                },
                methods: {
                    handleResponse(resp, callback = null) {
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
                        if (resp.tempFilename !== null) {
                            request.tempFilename = resp.tempFilename
                        }
                        $.get(action, request, (r)=> {
                            ConcreteToolbar.disableDirectExit()
                            var newBlock = block.replace(r)
                            ConcreteAlert.notify({
                                message: resp.message
                            })
                            if (callback !== null) {
                                return callback(newBlock);
                            }
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
                    cancelDesign() {
                        $('#ccm-inline-toolbar-container').hide();
                        ConcreteEvent.fire('EditModeExitInline');
                    },
                    saveDesign() {
                        const form = $('#ccm-inline-design-form');
                        form.concreteAjaxForm({
                            success:(resp) => {
                                this.handleResponse(resp, null)

                            },
                            error:(r) => {
                                $(this.$el).prependTo('#ccm-inline-toolbar-container').show()
                            }
                        })
                        $(this.$el).hide().prependTo(form);
                        form.submit();
                        ConcreteEvent.unsubscribe('EditModeExitInlineComplete');
                    },
                    refreshTemplate()
                    {
                        const form = $('#ccm-inline-design-form').detach();
                        this.handleResponse({
                            cID: this.pageId,
                            bID: this.blockId,
                            aID: this.areaId,
                            originalBlockID: this.blockId,
                            tempFilename: this.bFilename ?? '',
                            message: '<?=t('Template Loaded')?>'
                        }, (block)=>{
                            block.setActive(true);
                            block.getElem().addClass('ccm-block-edit-inline-active');
                            block.getElem().find('.ccm-block-edit').addClass('ccm-block-edit-inline-active')
                            form.appendTo(block.getElem().parent());

                            $.fn.dialog.hideLoader();
                        })
                    },
                }
            });
        });
    });
</script>
<?php } ?>