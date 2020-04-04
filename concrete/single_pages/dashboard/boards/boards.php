<?php

defined('C5_EXECUTE') or die("Access Denied.");

$permissions = new Permissions();
if ($permissions->canAddBoard()) { 
?>

<div class="ccm-dashboard-header-buttons">
    <a href="<?=$view->url('/dashboard/boards/add') ?>"
       class="btn btn-primary"><?php echo t("Add Board") ?></a>
</div>
<?php } ?>


<?php if (count($boards)) {
    ?>

    <ul class="item-select-list" id="ccm-stack-list">
        <?php foreach ($boards as $board) {
            $boardPermissions = new Permissions($board);
            if ($boardPermissions->canViewBoard()) {
            ?>

            <li>
                <a href="<?=URL::to('/dashboard/boards/details', 'view', $board->getBoardID())?>">
                    <i class="fas fa-th"></i> <?=$board->getBoardName()?>
                </a>
            </li>
            <?php
            }
        }
        ?>
    </ul>

    <?php

} else {
    ?>
    <p><?=t('You have not created any boards.')?></p>
    <?php

} ?>
