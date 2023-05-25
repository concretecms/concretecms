<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="mb-3">
    <label class="form-label"><?=$label?></label>
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
