<?php defined('C5_EXECUTE') or die("Access Denied.");

$navItems = $controller->getNavItems();

echo '<ul>';

foreach ($navItems as $ni) {

    echo '<li>';
    $name = (isset($translate) && $translate == true) ? t($ni->name) : $ni->name;
    echo $name;

    if ($ni->hasSubmenu) {
        echo '<ul>';
    } else {
        echo '</li>';
        echo str_repeat('</ul></li>', $ni->subDepth);
    }
}

echo '</ul>';
