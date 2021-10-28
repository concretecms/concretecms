<?php
defined('C5_EXECUTE') or die("Access Denied.");

if (!$previousLinkURL && !$nextLinkURL && !$parentLabel) {
    return false;
}
?>

<div class="ccm-block-next-previous">
    <?php
    if ($previousLinkURL && $previousLabel) {
        ?>
        <div class="ccm-block-next-previous-header">
            <h4><?php echo $previousLabel ?></h4>
        </div>
        <?php
    }

    if ($previousCollection) {
        $previousThumbnail = $previousCollection->getAttribute('thumbnail');
        if ($previousThumbnail) { ?>
            <div class="ccm-block-next-previous-thumbnail">
                <a href="<?=$previousLinkURL?>"><img src="<?=$previousThumbnail->getThumbnailURL('blog_entry_thumbnail')?>" class="img-fluid" /></a>
            </div>
        <?php } ?>

        <div class="ccm-block-next-previous-date">
            <?php echo $previousCollection->getCollectionDatePublicObject()->format('F j, Y • g:iA'); ?>
        </div>

        <?php
    }

    if ($previousLinkText) {
        ?>
        <div class="ccm-block-next-previous-previous-link">
            <?php echo $previousLinkURL ? '<a href="' . $previousLinkURL . '">' . $previousLinkText . '</a>' : '' ?>
        </div>
        <?php
    }

    if ($nextLinkURL && $nextLabel) {
        ?>
        <div class="ccm-block-next-previous-header">
            <h4><?php echo $nextLabel ?></h4>
        </div>
        <?php
    }

    if ($nextCollection) {
        $nextThumbnail = $nextCollection->getAttribute('thumbnail');
        if ($nextThumbnail) { ?>
            <div class="ccm-block-next-previous-thumbnail">
                <a href="<?=$nextLinkURL?>"><img src="<?=$nextThumbnail->getThumbnailURL('blog_entry_thumbnail')?>" class="img-fluid" /></a>
            </div>
        <?php } ?>


        <div class="ccm-block-next-previous-date">
            <?php echo $nextCollection->getCollectionDatePublicObject()->format('F j, Y • g:iA'); ?>
        </div>

    <?php
    }

    if ($nextLinkText) {
        ?>
        <div class="ccm-block-next-previous-next-link">
            <?php echo $nextLinkURL ? '<a href="' . $nextLinkURL . '">' . $nextLinkText . '</a>' : '' ?>
        </div>
        <?php
    }

    if ($parentLabel) {
        ?>
        <div class="ccm-block-next-previous-parent-link">
            <?php echo $parentLinkURL ? '<a href="' . $parentLinkURL . '">' . $parentLabel . '</a>' : '' ?>
        </div>
        <?php
    }
    ?>
</div>
