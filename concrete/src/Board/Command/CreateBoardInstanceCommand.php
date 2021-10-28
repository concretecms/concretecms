<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Foundation\Command\Command;

class CreateBoardInstanceCommand extends Command
{

    use BoardTrait;

    /**
     * @var string
     */
    protected $boardInstanceName;

    /**
     * @var Site
     */
    protected $site;

    /**
     * @return string
     */
    public function getBoardInstanceName():? string
    {
        return $this->boardInstanceName;
    }

    /**
     * @param string $boardInstanceName
     */
    public function setBoardInstanceName(string $boardInstanceName): void
    {
        $this->boardInstanceName = $boardInstanceName;
    }

    /**
     * @return Site
     */
    public function getSite(): Site
    {
        return $this->site;
    }

    /**
     * @param Site $site
     */
    public function setSite(Site $site): void
    {
        $this->site = $site;
    }


}
