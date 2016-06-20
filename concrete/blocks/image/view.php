<?php defined('C5_EXECUTE') or die("Access Denied.");

if (is_object($f) && $f->getFileID()) {
    if ($maxWidth > 0 || $maxHeight > 0) {
        $crop = false;

        $im = Core::make('helper/image');
        $thumb = $im->getThumbnail($f, $maxWidth, $maxHeight, $crop);

        $tag = new \HtmlObject\Image();
        $tag->src($thumb->src)->width($thumb->width)->height($thumb->height);
    } else {
        $image = Core::make('html/image', [$f]);
        $tag = $image->getTag();
    }

    $tag->addClass(' bID-'.$bID);
    if ($altText) {
        $tag->alt(h($altText));
    }

    if ($title) {
        $tag->title(h($title));
    }

    if ($linkURL) {
        echo '<a href="'.$linkURL.'">';
    }

    echo $tag;

    if ($linkURL) {
        echo '</a>';
    }
} elseif ($c->isEditMode()) {
    ?>
    <div class="ccm-edit-mode-disabled-item"><?php echo t('Empty Image Block.') ?></div>
    <?php
}

if (is_object($foS)): ?>
<script>
$(function() {
    $('.bID-<?php echo $bID; ?>')
        .mouseover(function(){$(this).attr("src", '<?php echo $imgPaths["hover"]; ?>');})
        .mouseout(function(){$(this).attr("src", '<?php echo $imgPaths["default"]; ?>');});
});
</script>
<?php
endif;
