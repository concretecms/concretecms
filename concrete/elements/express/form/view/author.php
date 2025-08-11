<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="list-group-item">
    <h6><?=$label?></h6>
    <div>
        <?php
        $author = $entry->getAuthor();
        if ($author) {
            print $author->getUserInfoObject()->getUserDisplayName();
        } else { ?>
            <span class="text-muted"><?=t('None')?></span>
            <?php
        }
        ?>
    </div>
</div>
