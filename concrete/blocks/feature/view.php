<?php  defined('C5_EXECUTE') or die('Access Denied.');
/** @var string|null $title the title of this feature block */
/** @var string|null $linkURL the link for this feature block */
/** @var string|null $titleFormat the title format tag, i.e. h1/h2/etc */
/** @var string|null $iconTag the icon and tag to use */
/** @var string $paragraph the paragraph/description for this block */
$title = $title ?? '';
$linkURL = $linkURL ?? null;
$title = h($title);
$titleFormat = $titleFormat ?? 'h4';
$iconTag = $iconTag ?? '';

if ($linkURL) {
    $title = '<a href="' . $linkURL . '">' . $title . '</a>';
}

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