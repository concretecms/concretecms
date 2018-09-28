<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Block\Block $b */

$bt = $b->getBlockTypeObject();

if ($b->getBlockTypeHandle() === BLOCK_HANDLE_SCRAPBOOK_PROXY) {
    $originalBlockId = $b->getController()->getOriginalBlockID();
    $originalBlock = Block::getByID($originalBlockId);
    $originalBlockType = $originalBlock->getBlockTypeObject();
    $supportsInlineEdit =  $originalBlockType->supportsInlineEdit();

    $cont = $originalBlock->getController();
}
else {
    $supportsInlineEdit = $bt->supportsInlineEdit();

    $cont = $bt->getController();
}
?>
            </div>
        <?php
        if (isset($extraParams) && is_array($extraParams)) {
            // defined within the area/content classes
            foreach ($extraParams as $key => $value) {
                ?><input type="hidden" name="<?= $key ?>" value="<?= $value ?>" /><?php
            }
        }
        if (!$b->getProxyBlock() && !$supportsInlineEdit) {
            ?>
            <div class="ccm-buttons dialog-buttons">
                <a href="javascript:clickedButton = true;$('#ccm-form-submit-button').get(0).click()" class="btn pull-right btn-primary"><?= t('Save') ?></a>
                <a style="float:left" href="javascript:void(0)" class="btn btn-default btn-hover-danger" onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></a>
            </div>
            <?php
        }
        ?>
        <input type="submit" name="ccm-edit-block-submit" value="submit" style="display: none" id="ccm-form-submit-button" />
    </form>
</div>