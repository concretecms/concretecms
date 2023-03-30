<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
$title = h($title);
$iconTag = $iconTag ?? '';

$opener = '<div class="ccm-block-feature-item">';
$closer = '</div>';


?>
<?=$opener?>
    <?php if ($title) {
    ?>

        <?=$iconTag?>

        <<?php echo $titleFormat; ?>><?=$title?></<?php echo $titleFormat; ?>>

    <?php
} ?>
    <?php
    if ($paragraph) {
        echo $paragraph;
    }
    if ($linkURL) {
        $link = '<a href="' . $linkURL . '">' . 'Read More' . '</a>';
        echo $link;
    }
    ?>
<?=$closer?>