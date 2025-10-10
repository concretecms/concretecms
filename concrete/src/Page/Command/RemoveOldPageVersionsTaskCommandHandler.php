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
        if (!($page && !$page->isError())) {
            $this->output->write(t('Skipped invalid page ID: %s', $command->getPageID()));
            return;
        }
        $pvl = new VersionList($page);
        $batchSize = 100;
        $offset = 10; // keep the 10 most recent versions intact
        while (true) {
            $versions = $pvl->get($batchSize, $offset);
            if (empty($versions)) {
                break;
            }
            $deletedInPass = 0;
            foreach ($versions as $v) {
                if ($v instanceof Version && !$v->isApproved() && !$v->isMostRecent()) {
                    try {
                        $v->delete();
                        ++$versionCount;
                        ++$deletedInPass;
                    } catch (\Throwable $e) {
                        // Swallow exception to continue processing other versions
                    }
                }
            }
            unset($versions);
            if ($deletedInPass === 0) {
                // Nothing deletable left beyond the 10 most recent
                break;
            }
        }

        $this->output->write(t('Scanned page ID: %s, removed versions: %s', $command->getPageID(), $versionCount));
    }
}