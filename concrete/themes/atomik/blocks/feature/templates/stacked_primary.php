<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
$title = h($title);
if ($linkURL) {
    $title = '<a href="' . $linkURL . '">' . $title . '</a>';
}
?>
<div class="ccm-block-feature-stacked">
    <?php if ($title) {
    ?>

        <i class="fas fa-<?=$icon?>"></i>

        <<?php echo $titleFormat; ?> class="text-primary"><?=$title?></<?php echo $titleFormat; ?>>
        
    <?php 
} ?>
    <?php
    if ($paragraph) {
        echo $paragraph;
    }
    ?>
</div>