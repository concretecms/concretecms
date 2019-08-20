<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Legacy\Loader;

Loader::element('panels/page', array(
'c' => $c,
));
