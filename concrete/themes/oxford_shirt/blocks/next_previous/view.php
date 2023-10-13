<?php
defined('C5_EXECUTE') or die("Access Denied.");

if (!$previousLinkURL && !$nextLinkURL && !$parentLabel) {
    return false;
}

?>

<div class="ccm-block-next-previous d-flex justify-content-between">
    <div class="ccm-block-next-previous-previous">
    <?php
    if ($previousLinkURL && $previousLabel) {
        ?>
        <a href="<?=$previousLinkURL?>" class="ccm-block-next-previous-header d-flex justify-content-between">
            <i class="fas fa-arrow-left"></i>
            <h4 class="subtitle-big"><?php echo $previousLabel ?></h4>
        </a>
        <?php
    }

    if ($previousCollection) {
        $user = UserInfo::getByID($previousCollection->getCollectionUserID());
        $previousThumbnail = $previousCollection->getAttribute('thumbnail');
        if ($previousThumbnail) { ?>
            <div class="ccm-block-next-previous-thumbnail">
                <a href="<?=$previousLinkURL?>"><img src="<?=$previousThumbnail->getThumbnailURL('blog_entry_thumbnail')?>" class="img-fluid" /></a>
            </div>
        <?php } ?>
        <?php if ($previousLinkText) {
        ?>
            <div class="ccm-block-next-previous-previous-link">
                <?php echo $previousLinkURL ? '<h3><a href="' . $previousLinkURL . '">' . $previousLinkText . '</a></h3>' : '' ?>
            </div>
        <?php
        } ?>
        <div class="ccm-block-next-previous-date">
            <?php echo $previousCollection->getCollectionDatePublicObject()->format('F j, Y, '); ?>
            <?php echo $user->getUserDisplayName(); ?>
        </div>
        <?php
    }
    ?>
    </div>

    <div class="ccm-block-next-previous-next">
    <?php if ($nextLinkURL && $nextLabel) {
        ?>
        <a href="<?=$nextLinkURL?>" class="ccm-block-next-previous-header d-flex justify-content-between">
            <h4 class="subtitle-big"><?php echo $nextLabel ?></h4>
            <i class="fas fa-arrow-right"></i>
        </a>
        <?php
    }

    if ($nextCollection) {
        $user = UserInfo::getByID($nextCollection->getCollectionUserID());
        $nextThumbnail = $nextCollection->getAttribute('thumbnail');
        if ($nextThumbnail) { ?>
            <div class="ccm-block-next-previous-thumbnail">
                <a href="<?=$nextLinkURL?>"><img src="<?=$nextThumbnail->getThumbnailURL('blog_entry_thumbnail')?>" class="img-fluid" /></a>
            </div>
        <?php } ?>
        <?php if ($nextLinkText) {
        ?>
            <div class="ccm-block-next-previous-next-link">
                <?php echo $nextLinkURL ? '<h3><a href="' . $nextLinkURL . '">' . $nextLinkText . '</a></h3>' : '' ?>
            </div>
        <?php
         } ?>
        <div class="ccm-block-next-previous-date">
            <?php echo $nextCollection->getCollectionDatePublicObject()->format('F j, Y, '); ?>
            <?php echo $user->getUserDisplayName(); ?>
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
</div>
