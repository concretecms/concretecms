<?php

namespace Concrete\Controller\Backend\Dashboard;

use Concrete\Core\Application\Service\Dashboard\Sitemap;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\EditResponse;
use Concrete\Core\Page\Page;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class SitemapUpdate extends AbstractController
{
    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    public function view(): Response
    {
        $this->checkUser();
        $this->updateDisplayOrder($this->getPageIDs());

        return $this->buildResponse();
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkUser(): void
    {
        $sh = $this->app->make(Sitemap::class);
        if (!$sh->canRead()) {
            throw new UserMessageException(t('Access Denied.'));
        }
    }

    /**
     * @return int[]
     */
    protected function getPageIDs(): array
    {
        $pageIDs = $this->request->request->get('cID', $this->request->query->get('cID'));
        if (!is_array($pageIDs)) {
            return [];
        }

        return array_values( // Reset array indexes
            array_filter( // Remove zeroes
                array_map( // Ensure integer types
                    'intval',
                    $pageIDs
                )
            )
        );
    }

    /**
     * @param int[] $pageIDs
     */
    protected function updateDisplayOrder(array $pageIDs): void
    {
        foreach ($pageIDs as $displayOrder => $pageID) {
            $c = Page::getByID($pageID);
            if ($c && !$c->isError()) {
                $c->updateDisplayOrder($displayOrder, $pageID);
            }
        }
    }

    protected function buildResponse(): Response
    {
        $r = new EditResponse();
        $r->setMessage(t('Display order saved.'));

        return $this->app->make(ResponseFactoryInterface::class)->json($r);
    }
}
