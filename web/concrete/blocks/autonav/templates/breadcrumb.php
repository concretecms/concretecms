<? defined('C5_EXECUTE') or die(_("Access Denied."));
$navItems = $controller->getNavItems();

foreach ($navItems as $ni) {
	if (!$ni->isFirst) {
		echo ' <span class="ccm-autonav-breadcrumb-sep">&gt;</span> ';
	}
	
	if ($ni->isCurrent) {
		echo $ni->name;
	} else {
		echo '<a href="' . $ni->url . '" target="' . $ni->target . '">' . $ni->name . '</a>';
	}
}
