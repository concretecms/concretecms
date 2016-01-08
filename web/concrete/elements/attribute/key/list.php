<?php
defined('C5_EXECUTE') or die("Access Denied.");

?>

<?php $form = Core::make('helper/form'); ?>

<? foreach($sortable_sets as $set) { ?>
    <h4><?=$set->getAttributeSetName()?></h4>
    <ul class="item-select-list ccm-attribute-list-wrapper" data-sortable-attribute-set="<?=$set->getAttributeSetID()?>">
        <?php
        foreach($set->getAttributeKeys() as $set_key) {
            $key = $set_key->getAttributeKey();
            $controller = $key->getController();
            $formatter = $controller->getIconFormatter(); ?>

            <li class="ccm-attribute" id="akID_<?=$key->getAttributeKeyID()?>">
                <a href="<?=$view->controller->getEditAttributeKeyURL($key)?>" title="<?php echo t('Handle')?>: <?php echo $key->getAttributeKeyHandle(); ?>">
                    <?=$formatter->getListIconElement()?>
                    <?=$key->getAttributeKeyDisplayName()?>
                </a>
                <? if ($enableSorting) { ?>
                    <i class="ccm-item-select-list-sort"></i>
                <? } ?>
            </li>

        <? } ?>
    </ul>
<? } ?>

<? if (count($unassigned)) { ?>
    <? if (count($sortable_sets)) { ?>
        <h4><?=t('Other')?></h4>
    <? } ?>
    <ul class="item-select-list ccm-attribute-list-wrapper">
        <?php
        foreach($unassigned as $key) {
            $controller = $key->getController();
            $formatter = $controller->getIconFormatter(); ?>

            <li class="ccm-attribute" id="akID_<?=$key->getAttributeKeyID()?>">
                <a href="<?=$view->controller->getEditAttributeKeyURL($key)?>" title="<?php echo t('Handle')?>: <?php echo $key->getAttributeKeyHandle(); ?>">
                    <?=$formatter->getListIconElement()?>
                    <?=$key->getAttributeKeyDisplayName()?>
                </a>
            </li>

        <? } ?>
    </ul>
<? } ?>

<?php
if ($enableSorting) { ?>
    <script type="text/javascript">
        $(function() {
            $("ul[data-sortable-attribute-set]").sortable({
                handle: 'i.ccm-item-select-list-sort',
                cursor: 'move',
                opacity: 0.5,
                stop: function() {
                    var req = $(this).sortable('serialize');
                    req += '&asID=' + $(this).attr('data-sortable-attribute-set');
                    req += '&ccm_token=' + '<?=Core::make("token")->generate('attribute_sort')?>';
                    $.post('<?=$view->controller->getSortAttributeCategoryURL()?>', req, function(r) {});
                }
            });
        });
    </script>
<?php } ?>

<?php if (isset($types) && is_array($types) && count($types) > 0) { ?>

    <h3><?=t('Add Attribute Type')?></h3>

    <div class="btn-group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
            <?=t('Choose')?> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <? foreach($types as $type) {
                $controller = $type->getController();
                /**
                 * @var $formatter \Concrete\Core\Attribute\IconFormatterInterface
                 */
                $formatter = $controller->getIconFormatter(); ?>
                <li><a href="<?=$view->controller->getAddAttributeTypeURL($type)?>"><?=$formatter->getListIconElement()?> <?=$type->getAttributeTypeDisplayName()?></a></li>
            <? } ?>
        </ul>
    </div>

<?php } ?>

