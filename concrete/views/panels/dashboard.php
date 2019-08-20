<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Legacy\Loader;

Loader::element('panels/dashboard', array(
'c' => $c,
'nav' => $nav,
'tab' => $tab,
'ui' => $ui,
));
