<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Page\Page;
use Concrete\Core\Search\Index\IndexManagerInterface;

class ClearPageIndexCommandHandler implements OutputAwareInterface
{

    use OutputAwareTrait;

    /**
     * @var IndexManagerInterface
     */
    protected $indexManager;

    public function __construct(IndexManagerInterface $indexManager)
    {
        $this->indexManager = $indexManager;
    }

    public function __invoke(ClearPageIndexCommand $command)
    {
        $this->output->write(t('Clearing page index...'));
        $this->indexManager->clear(Page::class);
    }

}
