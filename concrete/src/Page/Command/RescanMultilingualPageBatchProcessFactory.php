<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Stack\StackList;

class RescanMultilingualPageBatchProcessFactory implements BatchProcessFactoryInterface
{

    public function getBatchHandle()
    {
        return 'rescan_multilingual_page';
    }

    public function getCommands($section) : array
    {
        $pages = $section->populateRecursivePages(array(), array(
            'cID' => $section->getCollectionID(), ),
            $section->getCollectionParentID(), 0, false
        );

        // Add in all the stack pages found for the current locale.
        $list = new StackList();
        $list->filterByLanguageSection($section);
        $results = $list->get();
        foreach ($results as $result) {
            $pages[] = array('cID' => $result->getCollectionID());
        }

        $commands = [];
        foreach($pages as $page) {
            $commands[] = new RescanMultilingualPageCommand($page['cID']);
        }

        return $commands;
    }
}