<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div class="row">
    <div class="col-4">
        <?php
        $element = Element::get('dashboard/boards/menu', ['board' => $board, 'action' => 'rebuild']);
        $element->render();
        ?>
    </div>
    <div class="col-8">

        <form method="post" action="<?=$view->action('rebuild_board', $board->getBoardID())?>">
            <?=$token->output('rebuild_board')?>
            <p><?=t('Click below to rebuild this board? Warning! Any board customizations will be lost.')?></p>
            <button type"submit" class="btn btn-danger btn-lg"><?=t("Rebuild Board")?></button>

        </form>

        
    </div>
</div>
