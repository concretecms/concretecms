<?php
defined('C5_EXECUTE') or die("Access Denied.");

if (!$previousLinkURL && !$nextLinkURL && !$parentLabel) {
    return false;
}

?>

<div class="ccm-block-next-previous no-thumbnail d-flex justify-content-between">
    <div class="ccm-block-next-previous-previous">
        <?php if ($previousCollection) {
            $user = UserInfo::getByID($previousCollection->getCollectionUserID()); 
            
            if ($previousLinkText) { ?>
                <div class="ccm-block-next-previous-previous-link">
                    <?php echo $previousLinkURL ? '<h3><a href="' . $previousLinkURL . '">' . $previousLinkText . '</a></h3>' : '' ?>
                </div>
            <?php } ?>
            <div class="ccm-block-next-previous-date">
                <?php echo $previousCollection->getCollectionDatePublicObject()->format('F j, Y, '); ?>
                <?php echo $user->getUserDisplayName(); ?>
            </div>
            <?php if ($previousLinkURL && $previousLabel) {
            ?>
            <a href="<?=$previousLinkURL?>" class="ccm-block-next-previous-header d-flex">
                <i class="fas fa-arrow-left"></i>
                <h4 class="ms-3 subtitle-big"><?php echo $previousLabel ?></h4>
            </a>
            <?php } ?>
        <?php } ?>
    </div>

    <div class="ccm-block-next-previous-next">
    <?php if ($nextCollection) {
        $user = UserInfo::getByID($nextCollection->getCollectionUserID()); 
            if ($nextLinkText) { ?>
            <div class="ccm-block-next-previous-next-link text-end">
                <?php echo $nextLinkURL ? '<h3><a href="' . $nextLinkURL . '">' . $nextLinkText . '</a></h3>' : '' ?>
            </div>
        <?php } ?>
        <div class="ccm-block-next-previous-date text-end">
            <?php echo $nextCollection->getCollectionDatePublicObject()->format('F j, Y, '); ?>
            <?php echo $user->getUserDisplayName(); ?>
        </div>
        <?php if ($nextLinkURL && $nextLabel) { ?>
            <a href="<?=$nextLinkURL?>" class="ccm-block-next-previous-header d-flex justify-content-end">
                <h4 class="me-3 subtitle-big"><?php echo $nextLabel ?></h4>
                <i class="fas fa-arrow-right"></i>
            </a>
        <?php } ?>
    <?php } ?>

    <?php if ($parentLabel) {
        ?>
        <div class="ccm-block-next-previous-parent-link">
            <?php echo $parentLinkURL ? '<a href="' . $parentLinkURL . '">' . $parentLabel . '</a>' : '' ?>
        </div>
        <?php
    }
    ?>
        </div>
</div>
