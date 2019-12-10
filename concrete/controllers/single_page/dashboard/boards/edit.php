<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards;

use Concrete\Core\Board\Command\ResetBoardCustomWeightingCommand;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\DataSource\DataSource;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Core\Validation\SanitizeService;

class Edit extends DashboardSitePageController
{
    protected function getBoard($id)
    {
        $r = $this->entityManager->getRepository(Board::class);
        return $r->findOneByBoardID($id);
    }
    
    public function view($id = null)
    {
        $board = $this->getBoard($id);
        if (is_object($board)) {
            $configuredSources = $this->entityManager->getRepository(ConfiguredDataSource::class)
                ->findByBoard($board);
            $sources = $this->entityManager->getRepository(DataSource::class)->findAll();
            $this->set('board', $board);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }


}
