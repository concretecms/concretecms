<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Page;

class CopyPageCommandHandler
{

    public function __invoke(CopyPageCommand $command)
    {
        $oc = Page::getByID($command->getPageID());
        $db = \Database::connection();
        if ($oc && !$oc->isError()) {
            $cParentID = $db->getOne('select cParentID from Pages where cID = ?', [$command->getPageID()]);
            $newCID = $db->GetOne('select cID from QueuePageDuplicationRelations where originalCID = ? and queue_name = ?', array($cParentID, $command->getCopyBatchID()));
            if ($newCID > 0) {
                $dc = Page::getByID($newCID);
            } else {
                $dc = Page::getByID($command->getDestinationPageID());
            }

            if ($command->isMultilingualCopy()) {
                // Find multilingual section of the destination
                if (Section::isMultilingualSection($dc)) {
                    $ms = Section::getByID($dc->getCollectionID());
                } else {
                    $ms = Section::getBySectionOfSite($dc);
                }

                // Is page already copied?
                $existingCID = Section::getRelatedCollectionIDForLocale($command->getPageID(), $ms->getLocale());
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
                $ncID, $ocID, $command->getCopyBatchID(),
            ));
        }
    }


}