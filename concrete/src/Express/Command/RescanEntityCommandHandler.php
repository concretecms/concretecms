<?php
namespace Concrete\Core\Express\Command;

use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\Site\Service;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\ExpressEntryResults;
use Concrete\Core\Tree\Node\Type\ExpressEntrySiteResults;
use Doctrine\DBAL\Connection;

class RescanEntityCommandHandler implements LoggerAwareInterface
{

    use LoggerAwareTrait;

    /**
     * @var Connection
     */
    protected $db;

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_EXPRESS;
    }

    /**
     * @var Service
     */
    protected $siteService;

    public function __construct(Connection $db, Service $siteService)
    {
        $this->db = $db;
        $this->siteService = $siteService;
    }

    /**
     * Loops through all sites and ensures that the entity stored in the command has a
     * `express_entry_site_results` tree node exists for it.
     *
     * @param RescanEntityCommand $command
     */
    public function __invoke(RescanEntityCommand $command)
    {
        $node = Node::getByID($command->getEntity()->getEntityResultsNodeId());
        if ($node instanceof ExpressEntryResults) {
            foreach ($this->siteService->getList() as $site) {
                $siteResultsNode = $node->getSiteResultsNode($site);
                if (!$siteResultsNode) {
                    $siteNode = ExpressEntrySiteResults::add($site->getSiteName(), $node, $site);
                }
            }
        } else {
            $this->logger->warning(t('No results node found for entity %s during entity rescan.', $command->getEntity()->getHandle()));
        }

    }





}
