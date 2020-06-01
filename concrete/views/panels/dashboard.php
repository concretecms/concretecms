<?php

defined('C5_EXECUTE') or die("Access Denied.");
View::element('panels/dashboard', array(
'c' => $c,
'favorites' => $favorites,
'menu' => $menu,
'tab' => $tab,
'ui' => $ui,
));
