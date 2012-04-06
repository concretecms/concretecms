<? defined('C5_EXECUTE') or die(_("Access Denied."));
/**
 * The $navItems is provided by the controller. It is an array of objects, each representing a nav menu item.
 * The nav items array is a "flattened" one-dimensional list of all nav items -- it is not hierarchical,
 * even though it can represent a hierarchical menu structure.
 * You can look to various properties of each navItem object to determine its place in the menu hierarchy
 * (see below, for example $navItem->level, $navItem->subDepth, $navItem->hasSubmenu, etc.)
 * Items are ordered with the first top-level item first, followed by its sub-items, etc.
 * 
 * Each nav item object contains the following information:
 *	$navItem->url        : URL to the page
 *	$navItem->name       : page title (already escaped for html output)
 *	$navItem->target     : link target (e.g. "_self" or "_blank")
 *	$navItem->level      : number of levels deep the current menu item is from the top (top-level nav items are 1, their sub-items are 2, etc.)
 *	$navItem->subDepth   : number of levels deep the current menu item is *compared to the next item in the list* (useful for determining how many <ul>'s to close in a nested list)
 *	$navItem->hasSubmenu : true/false -- if this item has one or more sub-items (sometimes useful for CSS styling)
 *	$navItem->isFirst    : true/false -- if this is the first nav item *in its level* (for example, the first sub-item of a top-level item is TRUE)
 *	$navItem->isLast     : true/false -- if this is the last nav item *in its level* (for example, the last sub-item of a top-level item is TRUE)
 *	$navItem->isCurrent  : true/false -- if this nav item represents the page currently being viewed
 *	$navItem->inPath     : true/false -- if this nav item represents a parent page of the page currently being viewed (also true for the page currently being viewed)
 *	$navItem->attrClass  : Value of the 'nav_item_class' custom page attribute (if it exists and is set)
 *	$navItem->isEnabled  : true/false -- if the nav item should link to its page (this is only false if the 'disable_link_in_nav' custom page attribute is checked)
 *	$navItem->isHome     : true/false -- if this nav item represents the home page
 *	$navItem->cID        : collection id of the page this nav item represents
 *	$navItem->cObj       : collection object of the page this nav item represents (use this if you need to access page properties and attributes that aren't already available in the $navItem object)
 */


/** For extra functionality, you can add the following page attributes to your site (via Dashboard "Page Attributes")
 *
 * 1) Handle: replace_link_with_first_in_nav
 *    Type: Checkbox
 *    Functionality: If a page has this checked, clicking on it in the nav menu will go to its first child (sub-page) instead.
 *
 * 2) Handle: exclude_subpages_from_nav
 *    Type: Checkbox
 *    Functionality: If a page has this checked, all of that pages children (sub-pages) will be excluded from the nav menu (but the page itself will be included).
 *
 * 3) Handle: disable_link_in_nav
 *    Type: Checkbox
 *    Functionality: If a page has this checked, it will appear in the nav menu but will not be "clickable" (will not link to any page).
 *
 * 4) Handle: nav_item_class
 *    Type: Text
 *    Functionality: Whatever is entered into this textbox will be outputted as an additional CSS class for that page's nav item (NOTE: you must un-comment the "$ni->attrClass" code block in the CSS section below for this to work).
 */


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

	/*
	if ($ni->isFirst) {
		//class for the first item in each menu section (first top-level item, and first item of each dropdown sub-menu)
		$classes[] = 'nav-first';
	}
	*/

	/*
	if ($ni->isLast) {
		//class for the last item in each menu section (last top-level item, and last item of each dropdown sub-menu)
		$classes[] = 'nav-last';
	}
	*/

	/*
	if ($ni->hasSubmenu) {
		//class for items that have dropdown sub-menus
		$classes[] = 'nav-dropdown';
	}
	*/

	/*
	if (!empty($ni->attrClass)) {
		//class that can be set by end-user via the 'nav_item_class' custom page attribute
		$classes[] = $ni->attrClass;
	}
	*/

	/*
	if ($ni->isHome) {
		//home page
		$classes[] = 'nav-home';
	}
	*/

	/*
	//unique class for every single menu item
	$classes[] = 'nav-item-' . $ni->cID;
	*/

	//Put all classes together into one space-separated string
	$ni->classes = implode(" ", $classes);
}


//*** Step 2 of 2: Output menu HTML ***/

echo '<ul class="nav">'; //opens the top-level menu

foreach ($navItems as $ni) {

	echo '<li class="' . $ni->classes . '">'; //opens a nav item

	if ($ni->isEnabled) {
		echo '<a href="' . $ni->url . '" target="' . $ni->target . '" class="' . $ni->classes . '">' . $ni->name . '</a>';
	} else {
		echo '<span class="' . $ni->classes . '">' . $ni->name . '</span>';
	}

	if ($ni->hasSubmenu) {
		echo '<ul>'; //opens a dropdown sub-menu
	} else {
		echo '</li>'; //closes a nav item
		echo str_repeat('</ul></li>', $ni->subDepth); //closes dropdown sub-menu(s) and their top-level nav item(s)
	}
}

echo '</ul>'; //closes the top-level menu
