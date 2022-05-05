<?php defined('C5_EXECUTE') or die('Access Denied.');
use Concrete\Core\Localization\Localization;
use Concrete\Core\Page\Page;

/** @var string|null $mp4URL */
/** @var string|null $oggURL */
/** @var string|null $webmURL */
/** @var string|null $posterURL */
/** @var int|null $videoSize */
/** @var int|null $width */
?>

<div style="text-align:center; margin-top: 20px; margin-bottom: 20px;">
<?php
$c = Page::getCurrentPage();
if (is_object($c) && $c->isEditMode()) {
    $loc = Localization::getInstance();
    $loc->pushActiveContext(Localization::CONTEXT_UI);
    ?>
	<div class="ccm-edit-mode-disabled-item">
		<div style="padding: 8px;"><?php echo t('Content disabled in edit mode.'); ?></div>
	</div>
    <?php
    $loc->popActiveContext();
} elseif (!$webmURL && !$oggURL && !$mp4URL) {
?>
    <div class="ccm-edit-mode-disabled-item">
		<div style="padding: 8px;"><?php echo t('No Video Files Selected.'); ?></div>
    </div>
<?php
} elseif ($webmURL || $oggURL || $mp4URL) { ?>
    <video controls="controls" <?php echo $posterURL ? 'poster="' . $posterURL . '"' : '' ?>
    <?php if ($videoSize == 1) { ?>
    style="width: 100%"
    <?php } elseif ($videoSize == 2) { ?>
    width="<?php echo $width; ?>" style="max-width: 100%;"
    <?php } else { ?>
    style="max-width: 100%;"
    <?php
    }?>
    <?php echo $title ? 'title="' . h($title) . '"' : ''; ?>
    >
        <?php if ($webmURL) { ?>
        <source src="<?php echo $webmURL; ?>" type="video/webm">
        <?php
        }
        if ($oggURL) { ?>
        <source src="<?php echo $oggURL; ?>" type="video/ogg">
        <?php
        }
        if ($mp4URL) { ?>
        <source src="<?php echo $mp4URL; ?>" type="video/mp4">
        <?php
        }
        echo t("Your browser doesn't support the HTML5 video tag.");
        ?>
    </video>
<?php
}
?>
</div>