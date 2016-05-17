<?php defined('C5_EXECUTE') or die("Access Denied.");
$nh = Loader::helper('navigation');
$previousLinkURL = is_object($previousCollection) ? $nh->getLinkToCollection($previousCollection) : '';
$parentLinkURL = is_object($parentCollection) ? $nh->getLinkToCollection($parentCollection) : '';
$nextLinkURL = is_object($nextCollection) ? $nh->getLinkToCollection($nextCollection) : '';
$previousLinkText = is_object($previousCollection) ? $previousCollection->getCollectionName() : '';
$nextLinkText = is_object($nextCollection) ? $nextCollection->getCollectionName() : '';
?>

<?php if ($previousLinkURL || $nextLinkURL || $parentLinkText): ?>

<div class="ccm-block-next-previous-wrapper">
    <?php if ($previousLabel && $previousLinkURL != ''): ?>
    <div class="ccm-block-next-previous-header">
        <h5><?=$previousLabel?></h5>
    </div>
    <?php endif; ?>

    <?php if ($previousLinkText): ?>
	<p class="ccm-block-next-previous-previous-link">
		<?php echo $previousLinkURL ? '<a href="' . $previousLinkURL . '">' . $previousLinkText . '</a>' : '' ?>
 	</p>
	<?php endif; ?>

    <?php if ($nextLabel && $nextLinkURL != ''): ?>
        <div class="ccm-block-next-previous-header">
            <h5><?=$nextLabel?></h5>
        </div>
    <?php endif; ?>

    <?php if ($nextLinkText): ?>
        <p class="ccm-block-next-previous-next-link">
            <?php echo $nextLinkURL ? '<a href="' . $nextLinkURL . '">' . $nextLinkText . '</a>' : '' ?>
        </p>
    <?php endif; ?>

    <?php if ($parentLinkText): ?>
	<p class="ccm-block-next-previous-parent-link">
		<?php echo $parentLinkURL ? '<a href="' . $parentLinkURL . '">' . $parentLinkText . '</a>' : '' ?>
 	</p>
	<?php endif; ?>

</div>

<?php endif; ?>
