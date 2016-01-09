<?php
defined('C5_EXECUTE') or die("Access Denied.");

$newsflow = new Concrete\Core\Activity\Newsflow();

if (Loader::helper('validation/numbers')->integer($_REQUEST['cID'])) {
    $ed = $newsflow->getEditionByID($_REQUEST['cID']);
    if ($ed !== false) {
        print $ed->getContent();
    }
} else if (isset($_REQUEST['cPath'])) {
    $ed = $newsflow->getEditionByPath($_REQUEST['cPath']);
    if ($ed !== false) {
        print $ed->getContent();
    }
}
