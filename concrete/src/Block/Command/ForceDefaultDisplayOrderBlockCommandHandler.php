<?php
/**
 * Created by: Derek Cameron <info@derekcameron.com>
 * Copyright: 2021 Derek Cameron
 * Date: 2021/12/17
 **/

namespace Concrete\Core\Block\Command;


use Concrete\Core\Block\Block;
use Concrete\Core\Page\Page;

class ForceDefaultDisplayOrderBlockCommandHandler
{

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Doctrine\DBAL\Exception
     * @return void
     */
    public function __invoke(ForceDefaultDisplayOrderBlockCommand $command)
    {
        $page = Page::getByID($command->getPageID(), $command->getCollectionVersionID());
        if ($page && !$page->isError()) {
            $mc = Page::getByID($command->getOriginalPageID(), $command->getOriginalCollectionVersionID());
            if ($mc && !$mc->isError()) {
                $b = Block::getByID($command->getOriginalBlockID(), $mc, $command->getOriginalAreaHandle());

                if ($b && !$b->isError()) {
                    $copyBlock = Block::getByID($command->getBlockID(), $page, $command->getOriginalAreaHandle());
                    if ($copyBlock) {
                        $copyBlock->setAbsoluteBlockDisplayOrder($b->getBlockDisplayOrder());
                        // Rescan all the display order from this block
                        $page->rescanDisplayOrderFromBlock($copyBlock, $command->getAreaHandle(), 0);
                    }

                }
            }
        }
    }

}
