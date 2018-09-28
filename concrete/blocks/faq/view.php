<?php defined('C5_EXECUTE') or die('Access Denied.');

$linkCount = 1;
$faqEntryCount = 1;
?>

<div class="ccm-faq-container">
    <?php if (count($rows) > 0) { ?>
        <div class="ccm-faq-block-links">
            <?php foreach ($rows as $row) { ?>
                <a href="#<?php echo $bID . $linkCount; ?>"><?php echo $row['linkTitle']; ?></a>
                <?php
                ++$linkCount;
            } ?>
        </div>
        <div class="ccm-faq-block-entries">
            <?php foreach ($rows as $row) { ?>
                <div class="faq-entry-content">
                    <a name="<?php echo $bID . $faqEntryCount; ?>"></a>
                    <h3><?php echo $row['title']; ?></h3>
                    <?php echo $row['description']; ?>
                </div>
                <?php
                ++$faqEntryCount;
            } ?>
        </div>
    <?php
    } else {
    ?>
        <div class="ccm-faq-block-links">
            <p><?php echo t('No Faq Entries Entered.'); ?></p>
        </div>
    <?php
    }
    ?>
</div>
