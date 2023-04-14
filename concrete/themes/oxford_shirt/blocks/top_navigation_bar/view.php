<?php defined('C5_EXECUTE') or die('Access Denied.');
$c = Page::getCurrentPage();
?>

<div class="ccm-block-top-navigation-bar <?php if ($includeStickyNav && !$c->isEditMode()) { ?>fixed-top<?php } ?>" <?php if ($includeTransparency) { ?>style="display: none" data-transparency="navbar" <?php } ?>>
    <nav class="navbar w-100 align-items-center sidebar <?php if ($includeStickyNav && !$c->isEditMode()) { ?>fixed-top<?php } ?>">

        <?php if ($includeNavigation) { ?>
            <button class="navbar-toggler btn btn-link collapsed align-self-stretch align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#top-navigation-bar-<?= $bID ?>" aria-controls="#top-navigation-bar-<?= $bID ?>" aria-expanded="false" aria-label="<?= t('Toggle Navigation') ?>">
                <?php /* Custom animated Toggler */ ?>
                <div>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </div>
                <?php /* End animated toggler */ ?>
                <span class="icon-text">Menu</span>
                <?php
                /* Standard bootstrap toggler. Uncomment to use */
                /*
                ?>
                <span class="navbar-toggler-icon"></span>
                <?php */ ?>
            </button>
        <?php } ?>

        <?php if ($includeSearchInput) { ?>
            <button class="search-toggler btn btn-link collapsed align-self-stretch align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#top-search-bar-<?= $bID ?>" aria-controls="#top-search-bar-<?= $bID ?>" aria-expanded="false" aria-label="<?= t('Toggle Search') ?>">
                <i class="fas fa-search"></i>
                <span class="icon-text">Search</span>
            </button>
            <div class="collapse search-collapse" id="top-search-bar-<?= $bID ?>">
                <div class="container-fluid d-flex align-items-center">
                    <form method="get" action="<?= $searchAction ?>">
                        <div class="input-group flex-nowrap">
                            <span class="input-group-append">
                                <button class="btn" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </span>
                            <input class="form-control" type="search" name="query" placeholder="<?= t('Type here what are you searching for...') ?>" aria-label="<?= t('Search') ?>">
                        </div>
                    </form>
                    <button class="search-toggler btn btn-link align-self-stretch align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#top-search-bar-<?= $bID ?>" aria-expanded="false" aria-controls="top-search-bar-<?= $bID ?>">
                        <i class="fas fa-times"></i>
                        <span class="icon-text">Close</span>
                    </button>
                </div>
            </div>
        <?php } ?>

        <a class="navbar-brand" href="<?= $home->getCollectionLink() ?>">
            <?php if ($logo && ($includeBrandLogo && $includeBrandText)) { ?>
                <img src="<?= $logo->getURL() ?>" class="logo align-text-center">
                <?php if (isset($transparentLogo)) { ?>
                    <img src="<?= $transparentLogo->getURL() ?>" class="logo-transparent align-text-center">
                <?php } ?>
                <?= $brandingText ?>
            <?php } ?>
            <?php if ($logo && ($includeBrandLogo && !$includeBrandText)) { ?>
                <img src="<?= $logo->getURL() ?>" class="logo">
                <?php if (isset($transparentLogo)) { ?>
                    <img src="<?= $transparentLogo->getURL() ?>" class="logo-transparent">
                <?php } ?>
            <?php } ?>
            <?php if (!$includeBrandLogo && $includeBrandText) { ?>
                <?= $brandingText ?>
            <?php } ?>
        </a>

        <div class="collapse navbar-collapse" id="top-navigation-bar-<?= $bID ?>">
            <ul class="navbar-nav">
                <?php foreach ($navigation->getItems() as $item) {
                    /**
                     * @var $item \Concrete\Core\Navigation\Item\PageItem
                     */
                    if (count($item->getChildren()) > 0) { ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle<?= $item->isActive() ? " active" : ""; ?>" data-concrete-toggle="dropdown" target="<?= $controller->getPageItemNavTarget($item) ?>" href="<?= $item->getUrl() ?>">
                                <?= $item->getName() ?>
                            </a>
                            <ul class="dropdown-menu level-1">
                                <?php foreach ($item->getChildren() as $dropdownChild) { ?>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle <?= $dropdownChild->isActive() ? " active" : ""; ?>" target="<?= $controller->getPageItemNavTarget($dropdownChild) ?>" href="<?= $dropdownChild->getUrl() ?>"><?= $dropdownChild->getName() ?></a>
                                        <ul class="submenu dropdown-menu level-2">
                                            <li><a class="nav-link" href="#">Submenu item 1 </a></li>
                                            <li><a class="nav-link" href="#">Submenu item 2 </a></li>
                                            <li><a class="nav-link" href="#">Submenu item 3 </a>
                                                <ul class="submenu dropdown-menu level-3">
                                                    <li><a class="nav-link" href="#">Multi level 1</a></li>
                                                    <li><a class="nav-link" href="#">Multi level 2</a></li>
                                                    <li><a class="nav-link" href="#">Multi level 3</a></li>
                                                    <li><a class="nav-link" href="#">Multi level 4 </a>
                                                        <ul class="submenu dropdown-menu level-4">
                                                            <li><a class="nav-link" href="#">Multi level 5</a></li>
                                                            <li><a class="nav-link" href="#">Multi level 6</a></li>
                                                            <li><a class="nav-link" href="#">Multi level 7</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item"><a class="nav-link<?= $item->isActive() ? " active" : ""; ?>" target="<?= $controller->getPageItemNavTarget($item) ?>" href="<?= $item->getUrl() ?>"><?= $item->getName() ?></a></li>
                    <?php } ?>
                <?php } ?>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-comment-alt"></i>Topics</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-copy"></i>Collections</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-heart"></i>Liked</a></li>
            </ul>
        </div>
    </nav>
</div>

<script type="text/javascript">
	window.addEventListener("resize", function() {
		"use strict"; window.location.reload(); 
	});

	document.addEventListener("DOMContentLoaded", function(){
		 // make it as accordion for smaller screens
		if (window.innerWidth < 992) {

			document.querySelectorAll('.sidebar .nav-link').forEach(function(element){
				
				element.addEventListener('click', function (e) {

				  	let nextEl = element.nextElementSibling;
				  	let parentEl  = element.parentElement;
				  	let allSubmenus_array =	parentEl.querySelectorAll('.submenu');

				  	if(nextEl && nextEl.classList.contains('submenu')) {	
				  		e.preventDefault();	
				  		if(nextEl.style.display == 'block'){
				  			nextEl.style.display = 'none';

				  		} else {
				  			nextEl.style.display = 'block';
				  		}

				  	}
				});
			})
		}
		// end if innerWidth

	}); 
	// DOMContentLoaded  end
</script>