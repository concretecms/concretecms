<?php
defined('C5_EXECUTE') or die("Access Denied.");
$th = Loader::helper('text');
$c = Page::getCurrentPage();
$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
?>

<script>	
	document.addEventListener('DOMContentLoaded', function () {	
		var mySwiper = new Swiper ('.swiper-container', {
			// API: http://idangero.us/swiper/api/
			// Demos: http://idangero.us/swiper/demos/
			
			//General
			speed: 300,
			autoHeight: false,
			initialSlide: 0,
			
			//A.Autoplay
			autoplay: false,
			
			//B.Slides grid
			spaceBetween: 20,
			centeredSlides: true,
			
			//C.Navigation controls
			//c.1. arrows
			nextButton: '.swiper-button-next',
			prevButton: '.swiper-button-prev',

			// c.2. pagination
			// Can be "bullets", "fraction", "progress" or "custom"
			pagination: '.swiper-pagination',
			paginationClickable: true,
			paginationType: 'bullets',
			
			//D. Grab Cursor
			grabCursor: true,
			
			//E. Clicks: Set to true and click on any slide will produce transition to this slide
			slideToClickedSlide: false,
			
			//F. a11y // Option to enable keyboard accessibility to provide foucsable navigation buttons and basic ARIA for screen readers
			a11y: true,
			
			//G. Keyboard / Mousewheel
			keyboardControl: false,
			mousewheelControl: false,
			
			//H. Images
			preloadImages: true,
			lazyLoading: true,
			
			//I. Loop
			loop: false,
      
			//J. Freemode
			freeMode: false,
			
			//K. EFFECTS / Could be "slide", "fade", "cube", "coverflow" or "flip"
			effect: 'slide',
			
			//L. Responsive Layout parameters
			slidesPerView: 3, // Dont work fine with "effect: fade;"
			spaceBetween: 30,
			// Responsive breakpoints
			breakpoints: {
				// when window width is <= 320px
				320: {
					slidesPerView: 1,
					spaceBetween: 10
				},
				// when window width is <= 480px
				480: {
					slidesPerView: 1,
					spaceBetween: 20
				},
				// when window width is <= 640px
				640: {
					slidesPerView: 1,
					spaceBetween: 30
				}
			}
		})      
	});
</script>


<?php if ($c->isEditMode() && $controller->isBlockEmpty()) {
    ?>
    <div class="ccm-edit-mode-disabled-item"><?php echo t('Empty Page List Block.')?></div>
<?php
} else {
    ?>

<div class="ccm-block-page-list-wrapper swiper-container">

    <?php if (isset($pageListTitle) && $pageListTitle): ?>
        <div class="ccm-block-page-list-header">
            <h5><?php echo h($pageListTitle)?></h5>
        </div>
    <?php endif;
    ?>

    <?php if (isset($rssUrl) && $rssUrl): ?>
        <a href="<?php echo $rssUrl ?>" target="_blank" class="ccm-block-page-list-rss-feed"><i class="fa fa-rss"></i></a>
    <?php endif;
    ?>

    <div class="ccm-block-page-list-pages swiper-wrapper">

    <?php

    $includeEntryText = false;
    if (
        (isset($includeName) && $includeName)
        ||
        (isset($includeDescription) && $includeDescription)
        ||
        (isset($useButtonForLink) && $useButtonForLink)
    ) {
        $includeEntryText = true;
    }

    foreach ($pages as $page):

        // Prepare data for each page being listed...
        $buttonClasses = 'ccm-block-page-list-read-more';
    $entryClasses = 'ccm-block-page-list-page-entry swiper-slide';
    $title = $th->entities($page->getCollectionName());
    $url = ($page->getCollectionPointerExternalLink() != '') ? $page->getCollectionPointerExternalLink() : $nh->getLinkToCollection($page);
    $target = ($page->getCollectionPointerExternalLink() != '' && $page->openCollectionPointerExternalLinkInNewWindow()) ? '_blank' : $page->getAttribute('nav_target');
    $target = empty($target) ? '_self' : $target;
    $description = $page->getCollectionDescription();
    $description = $controller->truncateSummaries ? $th->wordSafeShortText($description, $controller->truncateChars) : $description;
    $description = $th->entities($description);
    $thumbnail = false;
    if ($displayThumbnail) {
        $thumbnail = $page->getAttribute('thumbnail');
    }
    if (is_object($thumbnail) && $includeEntryText) {
        $entryClasses = 'ccm-block-page-list-page-entry-horizontal swiper-slide';
    }

    $date = $dh->formatDateTime($page->getCollectionDatePublic(), true);

        //Other useful page data...

        //$last_edited_by = $page->getVersionObject()->getVersionAuthorUserName();

        //$original_author = Page::getByID($page->getCollectionID(), 1)->getVersionObject()->getVersionAuthorUserName();

        /* CUSTOM ATTRIBUTE EXAMPLES:
         * $example_value = $page->getAttribute('example_attribute_handle');
         *
         * HOW TO USE IMAGE ATTRIBUTES:
         * 1) Uncomment the "$ih = Loader::helper('image');" line up top.
         * 2) Put in some code here like the following 2 lines:
         *      $img = $page->getAttribute('example_image_attribute_handle');
         *      $thumb = $ih->getThumbnail($img, 64, 9999, false);
         *    (Replace "64" with max width, "9999" with max height. The "9999" effectively means "no maximum size" for that particular dimension.)
         *    (Change the last argument from false to true if you want thumbnails cropped.)
         * 3) Output the image tag below like this:
         *		<img src="<?php echo $thumb->src ?>" width="<?php echo $thumb->width ?>" height="<?php echo $thumb->height ?>" alt="" />
         *
         * ~OR~ IF YOU DO NOT WANT IMAGES TO BE RESIZED:
         * 1) Put in some code here like the following 2 lines:
         * 	    $img_src = $img->getRelativePath();
         *      $img_width = $img->getAttribute('width');
         *      $img_height = $img->getAttribute('height');
         * 2) Output the image tag below like this:
         * 	    <img src="<?php echo $img_src ?>" width="<?php echo $img_width ?>" height="<?php echo $img_height ?>" alt="" />
         */

        /* End data preparation. */

        /* The HTML from here through "endforeach" is repeated for every item in the list... */ ?>

        <div class="<?php echo $entryClasses?>">

        <?php if (is_object($thumbnail)): ?>
            <div class="ccm-block-page-list-page-entry-thumbnail">
                <?php
                $img = Core::make('html/image', array($thumbnail));
    $tag = $img->getTag();
    $tag->addClass('img-responsive');
    echo $tag;
    ?>
            </div>
        <?php endif;
    ?>

        <?php if ($includeEntryText): ?>
            <div class="ccm-block-page-list-page-entry-text">

                <?php if (isset($includeName) && $includeName): ?>
                <div class="ccm-block-page-list-title">
                    <?php if (isset($useButtonForLink) && $useButtonForLink) {
    ?>
                        <?php echo $title;
    ?>
                    <?php
} else {
    ?>
                        <a href="<?php echo $url ?>" target="<?php echo $target ?>"><?php echo $title ?></a>
                    <?php
}
    ?>
                </div>
                <?php endif;
    ?>

                <?php if (isset($includeDate) && $includeDate): ?>
                    <div class="ccm-block-page-list-date"><?php echo $date?></div>
                <?php endif;
    ?>

                <?php if (isset($includeDescription) && $includeDescription): ?>
                    <div class="ccm-block-page-list-description">
                        <?php echo $description ?>
                    </div>
                <?php endif;
    ?>

                <?php if (isset($useButtonForLink) && $useButtonForLink): ?>
                <div class="ccm-block-page-list-page-entry-read-more">
                    <a href="<?php echo $url?>" target="<?php echo $target?>" class="<?php echo $buttonClasses?>"><?php echo $buttonLinkText?></a>
                </div>
                <?php endif;
    ?>

                </div>
        <?php endif;
    ?>
        </div>

	<?php endforeach;
    ?>
	
	
    </div>

    <?php if (count($pages) == 0): ?>
        <div class="ccm-block-page-list-no-pages"><?php echo h($noResultsMessage)?></div>
    <?php endif;
    ?>
	
	<!-- If we need pagination -->
		<div class="swiper-pagination"></div>
		<div class="nav-wrapper">
			<!-- If we need navigation buttons -->
			<div class="swiper-button-prev"></div>
			<div class="swiper-button-next"></div>
			
			
		</div>
		

</div><!-- end .ccm-block-page-list -->


<?php if ($showPagination): ?>
    <?php echo $pagination;
    ?>
<?php endif;
    ?>

<?php
} ?>
