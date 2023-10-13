<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="blog-author-byline">

    <?php if ($author) { ?>

        <?php
        $avatar = $author->getAvatar();
        if ($avatar) {
            ?>
            <div class="blog-author-byline-avatar"><img src="<?=$avatar?>"></div>
        <?php } ?>

    <?php } ?>

    <div class="blog-author-byline-date">
        <?=date('F d, Y • g:iA', (string) $date)?>
    </div>

</div>
