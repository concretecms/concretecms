<? defined('C5_EXECUTE') or die("Access Denied.");
$navItems = $controller->getNavItems(true);

for ($i = 0; $i < count($navItems); $i++) {
	$ni = $navItems[$i];
	if ($i > 0) {
		echo ' <span class="ccm-autonav-breadcrumb-sep">&gt;</span> ';
	}
	
	if ($ni->isCurrent) {
		echo $ni->name;
	} else {
		echo '<a href="' . $ni->url . '" target="' . $ni->target . '">' . $ni->name . '</a>';
	}
}
