<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div style="text-align:center; margin-top: 20px; margin-bottom: 20px;">
<?php
$c = Page::getCurrentPage();
if ($c->isEditMode()) {
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