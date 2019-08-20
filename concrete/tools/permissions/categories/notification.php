<?php

defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\Foundation\Environment;

$env = Environment::get();
$file = $env->getPath(DIRNAME_TOOLS . '/permissions/categories/admin.php');
require $file;
exit;
