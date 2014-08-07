<?php
defined('C5_EXECUTE') or die("Access Denied.");
$th = Loader::helper('text');
?>

<div class="ccm-block-page-list-thumbnail-grid-wrapper">

	<?php foreach ($pages as $page):

		$title = $th->entities($page->getCollectionName());
		$url = $nh->getLinkToCollection($page);
		$target = ($page->getCollectionPointerExternalLink() != '' && $page->openCollectionPointerExternalLinkInNewWindow()) ? '_blank' : $page->getAttribute('nav_target');
		$target = empty($target) ? '_self' : $target;
		$thumbnail = $page->getAttribute('thumbnail');

        ?>

        <div class="ccm-block-page-list-page-entry-grid-item">

        <?php if (is_object($thumbnail)): ?>
            <div class="ccm-block-page-list-page-entry-grid-thumbnail">
                <a href="<?php echo $url ?>" target="<?php echo $target ?>"><?
                $img = Core::make('html/image', array($thumbnail));
                $tag = $img->getTag();
                $tag->addClass('img-responsive');
                print $tag;
                ?>
                    <div class="ccm-block-page-list-page-entry-grid-thumbnail-hover">
                        <div class="ccm-block-page-list-page-entry-grid-thumbnail-title-wrapper">
                        <div class="ccm-block-page-list-page-entry-grid-thumbnail-title">
                            <i class="ccm-block-page-list-page-entry-grid-thumbnail-icon"></i>
                            <?=$title?>
                        </div>
                        </div>
                    </div>

                </a>

            </div>
        <? endif; ?>

        </div>

	<?php endforeach; ?>

    <? if (count($pages) == 0): ?>
        <div class="ccm-block-page-list-no-pages"><?=$noResultsMessage?></div>
    <? endif;?>

</div>

<?php if ($showPagination): ?>
    <?php echo $pagination;?>
<?php endif; ?>