<?php

defined('C5_EXECUTE') or die("Access Denied.");

$navItems = $controller->getNavItems();
$c = Page::getCurrentPage();
/*** STEP 1 of 2: Determine all CSS classes (only 2 are enabled by default, but you can un-comment other ones or add your own) ***/
foreach ($navItems as $ni) {
    $classes = array();
    if ($ni->isCurrent) {
        //class for the page currently being viewed
        $classes[] = 'nav-selected';
    }
    if ($ni->inPath) {
        //class for parent items of the page currently being viewed
        $classes[] = 'nav-path-selected';
    }
    $ni->classes = implode(" ", $classes);
}

//*** Step 2 of 2: Output menu HTML ***/

if (count($navItems) > 0) {
    echo '<ul class="nav">'; //opens the top-level menu

    for ($i = 0; $i < count($navItems); $i++) {
        $ni = $navItems[$i];
        echo '<li class="' . $ni->classes . '">'; //opens a nav item
        echo '<a href="' . $ni->url . '" target="' . $ni->target . '" class="' . $ni->classes . '">' . $ni->name . '</a>';
        if ($ni->hasSubmenu) {
            echo '<ul>'; //opens a dropdown sub-menu
        } else {
            echo '</li>'; //closes a nav item
            echo str_repeat('</ul></li>', $ni->subDepth); //closes dropdown sub-menu(s) and their top-level nav item(s)
        }
        $next = $navItems[$i + 1];
        if ($ni->cObj->getAttribute('is_desktop') || is_object($next) && $next->cObj->getPackageID() > 0 && $ni->cObj->getPackageID() == 0) {
            echo '<li class="nav-divider"></li>';
        }

    }
    echo '</ul>'; //closes the top-level menu
}
