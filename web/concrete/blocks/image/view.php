<?php defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
if (is_object($f)) {
    $tag = Core::make('html/image', array($f))->getTag();
    $tag->addClass('ccm-image-block img-responsive bID-'.$bID);
    if ($altText) {
        $tag->alt($altText);
    }
    if ($title) {
        $tag->title($title);
    }
    if ($linkURL):
        print '<a href="' . $linkURL . '">';
    endif;

    print $tag;

    if ($linkURL):
        print '</a>';
    endif;
} else if ($c->isEditMode()) { ?>

    <div class="ccm-edit-mode-disabled-item"><?=t('Empty Image Block.')?></div>

<? } ?>
<script>
$(function() {
    $('.bID-<?php print $bID;?>')
        .mouseover(function(e){$(this).attr("src", '<?php print $imgPath["hover"];?>');})
        .mouseout(function(e){$(this).attr("src", '<?php print $imgPath["default"];?>');});
});
</script>