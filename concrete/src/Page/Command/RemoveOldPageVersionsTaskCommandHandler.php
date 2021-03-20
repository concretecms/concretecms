<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Page\Collection\Version\Version;
use Concrete\Core\Page\Collection\Version\VersionList;
use Concrete\Core\Page\Page;

class RemoveOldPageVersionsTaskCommandHandler implements OutputAwareInterface
{

    use OutputAwareTrait;

    /**
     * @param RemoveOldPageVersionsTaskCommand $command
     */
    public function __invoke(RemoveOldPageVersionsTaskCommand $command)
    {
        $versionCount = 0;
        $page = Page::getByID($command->getPageID());
        $pvl = new VersionList($page);
        foreach (array_slice($pvl->get(), 10) as $v) {
            if ($v instanceof Version && !$v->isApproved() && !$v->isMostRecent()) {
                $v->delete();
                ++$versionCount;
            }
        }

        $this->output->write(t('Scanned page ID: %s, removed versions: %s', $command->getPageID(), $versionCount));

    }


}