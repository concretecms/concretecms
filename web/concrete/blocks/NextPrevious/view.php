<?php defined('C5_EXECUTE') or die("Access Denied.");

$nh = Loader::helper('navigation');
$previousLinkURL = is_object($previousCollection) ? $nh->getLinkToCollection($previousCollection) : '';
$parentLinkURL = is_object($parentCollection) ? $nh->getLinkToCollection($parentCollection) : '';
$nextLinkURL = is_object($nextCollection) ? $nh->getLinkToCollection($nextCollection) : '';
?>

<div id="ccm-next-previous-<?php echo $bID; ?>" class="ccm-next-previous-wrapper">

    <?php if ($previousLinkText): ?>
	<div class="ccm-next-previous-previouslink">
		<?php echo $previousLinkURL ? '<a href="' . $previousLinkURL . '">' . $previousLinkText . '</a>' : '&nbsp;' ?>
 	</div>
	<?php endif; ?>

	<?php if ($parentLinkText): ?>
	<div class="ccm-next-previous-parentlink">
		<?php echo $parentLinkURL ? '<a href="' . $parentLinkURL . '">' . $parentLinkText . '</a>' : '' ?>
 	</div>
	<?php endif; ?>
	
	<?php if ($nextLinkText): ?>
	<div class="ccm-next-previous-nextlink">
		<?php echo $nextLinkURL ? '<a href="' . $nextLinkURL . '">' . $nextLinkText . '</a>' : '' ?>
 	</div>
	<?php endif; ?>

	<div class="spacer"></div>
</div>
