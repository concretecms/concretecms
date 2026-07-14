<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
$title = h($title);
$iconTag = $iconTag ?? '';

$opener = '<div class="ccm-block-feature-stacked">';
$closer = '</div>';
if ($linkURL) {
    $safeLinkURL = h($linkURL);
    $opener = '<a href="' . $safeLinkURL . '" class="ccm-block-feature-stacked">';
    $closer = '</a>';
}

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
    ?>
<?=$closer?>
