<?php
defined('C5_EXECUTE') or die("Access Denied.");

if (!$previousLinkURL && !$nextLinkURL && !$parentLabel) {
    return false;
}
?>

<div class="ccm-block-next-previous simple-next-prev gx-5">
    <?php
    if ($previousLinkURL && $previousLabel) {
        ?>
        <div class="ccm-block-next-previous-header">
            <h5><?php echo $previousLabel ?></h5>
        </div>
        <?php
    }

    if ($previousLinkText) {
        ?>
        <p class="ccm-block-next-previous-previous-link">
            <?php echo $previousLinkURL ? '<a class="btn btn-light w-100" href="' . $previousLinkURL . '">' . '<span class="me-3"><i class="fas fa-arrow-left"></i></span>' . 'Previous' . '</a>' : '' ?>
        </p>
        <?php
    }

    if ($nextLinkURL && $nextLabel) {
        ?>
        <div class="ccm-block-next-previous-header">
            <h5><?php echo $nextLabel ?></h5>
        </div>
        <?php
    }

    if ($nextLinkText) {
        ?>
        <p class="ccm-block-next-previous-next-link">
            <?php echo $nextLinkURL ? '<a class="btn btn-secondary w-100" href="' . $nextLinkURL . '">' . 'Next' . '<span class="ms-3"><i class="fas fa-arrow-right"></i></span>' . '</a>' : '' ?>
        </p>
        <?php
    }

    if ($parentLabel) {
        ?>
        <p class="ccm-block-next-previous-parent-link">
            <?php echo $parentLinkURL ? '<a href="' . $parentLinkURL . '">' . $parentLabel . '</a>' : '' ?>
        </p>
        <?php
    }
    ?>
</div>
