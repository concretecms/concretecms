<?php

use Concrete\Core\Legacy\Loader;
use Concrete\Core\Page\Collection\Collection;

session_write_close();

defined('C5_EXECUTE') or die("Access Denied.");
if (Loader::helper('validation/token')->validate()) {
    Collection::reindexPendingPages();
} else {
    echo "Access Denied.";
}
