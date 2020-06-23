<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface;
use Concrete\Core\Page\Stack\StackList;

class RescanMultilingualPageBatchProcessFactory implements BatchProcessFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface::getBatchHandle()
     */
    public function getBatchHandle(): string
    {
        return 'rescan_multilingual_page';
    }

    /**
     * {@inheritdoc}
     *
     * @param \Concrete\Core\Multilingual\Page\Section\Section $section
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface::getCommands()
     */
    public function getCommands($section): array
    {
        $pages = $section->populateRecursivePages(
            [],
            [
                'cID' => $section->getCollectionID(), ],
            $section->getCollectionParentID(),
            0,
            false
        );

        // Add in all the stack pages found for the current locale.
        $list = new StackList();
        $list->filterByLanguageSection($section);
        $results = $list->get();
        foreach ($results as $result) {
            $pages[] = ['cID' => $result->getCollectionID()];
        }

        $commands = [];
        foreach ($pages as $page) {
            $commands[] = new RescanMultilingualPageCommand($page['cID']);
        }

        return $commands;
    }
}
