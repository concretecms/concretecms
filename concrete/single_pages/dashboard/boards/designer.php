<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

    <div class="ccm-dashboard-header-buttons">
        <a href="<?=$view->action('add')?>" class="btn btn-secondary"><?=t('Create Custom Board Element')?></a>
    </div>


    <div class="help-block"><?=t('Create custom slots and place them in your boards.')?></div>

<h3><?=t('Drafts')?></h3>

<?php if ($elements && count($elements)) { ?>

    <ul class="item-select-list">
        <?php foreach($elements as $element) {
            $name = t('(No Name)');
            if ($element->getElementName()) {
                $name = $element->getElementName();
            }
            $created = $element->getDateCreatedDateTime();
            ?>
            <li><a href="<?=$view->url('/dashboard/boards/designer/choose_items', $element->getID())?>">
                    <i class="fa fa-th"></i>
                    <?=$name?>
                    <span class="text-muted float-right"><?=$created->format('F d, Y g:i a')?></span>
                </a></li>
        <?php } ?>
    </ul>
<?php } else { ?>
    <?=t('You are not currently working on any custom slots.')?>
<?php } ?>
