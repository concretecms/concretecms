<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Multilingual\Page\Section\Section;

// we have to do this otherwise permissions pointers aren't correct
// (cInheritPermissionsFromCID on parent nodes)
Core::make('cache/request')->disable();

$q = Queue::get('copy_page');
$includeParent = true;
if (isset($_REQUEST['copyChildrenOnly']) && $_REQUEST['copyChildrenOnly']) {
    $includeParent = false;
}
$db = Loader::db();

if (isset($_POST['process']) && $_POST['process']) {
    $obj = new stdClass();
    $js = Loader::helper('json');
    $messages = $q->receive(Config::get('concrete.limits.copy_pages'));
    foreach ($messages as $key => $p) {
        // delete the page here
        $page = unserialize($p->body);
        $oc = Page::getByID($page['cID']);
        // this is the page we're going to copy.
        // now we check to see if the parent ID of the current record has already been duplicated somewhere.
        $newCID = $db->GetOne('select cID from QueuePageDuplicationRelations where originalCID = ? and queue_name = ?', array($page['cParentID'], 'copy_page'));
        if ($newCID > 0) {
            $dc = Page::getByID($newCID);
        } else {
            $dc = Page::getByID($page['destination']);
        }

        if (isset($_POST['multilingual']) && $_POST['multilingual']) {
            // Find multilingual section of the destination
            if (Section::isMultilingualSection($dc)) {
                $ms = Section::getByID($dc->getCollectionID());
            } else {
                $ms = Section::getBySectionOfSite($dc);
            }

            // Is page already copied?
            $existingCID = Section::getRelatedCollectionIDForLocale($page['cID'], $ms->getLocale());
            if ($existingCID) {
                $nc = Page::getById($existingCID);

                if ($dc->getCollectionID() != $nc->getCollectionParentID()) {
                    $nc->move($dc);
                }
            } else {
                $nc = $oc->duplicate($dc);
            }
        } else {
            $nc = $oc->duplicate($dc);
        }

        $ocID = $oc->getCollectionID();
        $ncID = $nc->getCollectionID();
        if ($oc->getCollectionPointerOriginalID() > 0) {
            $ocID = $oc->getCollectionPointerOriginalID();
        }
        if ($nc->getCollectionPointerOriginalID() > 0) {
            $ncID = $nc->getCollectionPointerOriginalID();
        }
        $db->Execute('insert into QueuePageDuplicationRelations (cID, originalCID, queue_name) values (?, ?, ?)', array(
            $ncID, $ocID, 'copy_page',
        ));

        $q->deleteMessage($p);
    }

    $obj->totalItems = $q->count();
    echo $js->encode($obj);
    if ($q->count() == 0) {
        $q->deleteQueue('copy_page');
        $db->Execute('truncate table QueuePageDuplicationRelations');
    }
    exit;
} elseif ($q->count() == 0) {
}
$totalItems = $q->count();
Loader::element('progress_bar', array('totalItems' => $totalItems, 'totalItemsSummary' => t2("%d page", "%d pages", $totalItems)));
