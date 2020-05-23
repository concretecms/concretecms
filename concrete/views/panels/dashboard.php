<?php

defined('C5_EXECUTE') or die("Access Denied.");
View::element('panels/dashboard', array(
'c' => $c,
'navigation' => $navigation,
'tab' => $tab,
'ui' => $ui,
));
