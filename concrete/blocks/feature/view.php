<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
$title = h($title);
if ($linkURL) {
    $title = '<a href="' . $linkURL . '">' . $title . '</a>';
}
$iconTag = $iconTag ?? '';
?>
<div class="ccm-block-feature-item">
    <?php if ($title) {
    ?>
        
        <<?php echo $titleFormat; ?>><?=$iconTag?> <?=$title?></<?php echo $titleFormat; ?>>
        
    <?php 
} ?>
    <?php
    if ($paragraph) {
        echo $paragraph;
    }
    ?>
</div>