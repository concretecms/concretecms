<?php

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Page\Stack\Stack[] $stacks */
?>
<div class="ccm-panel-add-folder-stack-list">
    <?php
        View::element('panels/add/stack_list', ['stacks' => $stacks, 'c' => $c]);
    ?>
</div>
