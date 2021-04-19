<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var int $width
 * @var int $height
 * @var int $zoom
 * @var float $latitude
 * @var float $longitude
 * @var bool $scrollwheel
 */

$c = Page::getCurrentPage();
if ($c->isEditMode()) {
    $loc = Localization::getInstance();
    $loc->pushActiveContext(Localization::CONTEXT_UI);
    ?>
    <div class="ccm-edit-mode-disabled-item" style="width: <?= $width; ?>; height:  <?= $height; ?>">
        <div style="padding: 80px 0 0 0"><?= t('Google Map disabled in edit mode.') ?></div>
    </div>
    <?php
    $loc->popActiveContext();
} else { ?>
    <?php if (strlen($title) > 0) { ?><<?php echo $titleFormat; ?>><?= $title; ?></<?php echo $titleFormat; ?>><?php } ?>
    <div class="googleMapCanvas"
         style="width: <?= $width; ?>; height: <?= $height; ?>"
         data-zoom="<?= $zoom; ?>"
         data-latitude="<?= $latitude; ?>"
         data-longitude="<?= $longitude; ?>"
         data-scrollwheel="<?= (bool) $scrollwheel ? 'true' : 'false'; ?>"
         data-draggable="<?= (bool) $scrollwheel ? 'true' : 'false'; ?>"
    >
    </div>
<?php } ?>