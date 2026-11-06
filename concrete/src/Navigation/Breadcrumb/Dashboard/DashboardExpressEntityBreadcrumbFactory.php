<?php

declare(strict_types=1);

namespace Concrete\Core\Navigation\Breadcrumb\Dashboard;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Navigation\Breadcrumb\BreadcrumbInterface;
use Concrete\Core\Navigation\Item\Item;
use Concrete\Core\Page\Page;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Builds the breadcrumb of the dashboard pages that manage an Express entity
 * (the entity details and the /dashboard/system/express/entities/... child pages).
 */
class DashboardExpressEntityBreadcrumbFactory
{
    /**
     * The path of the dashboard page listing the Express entities.
     *
     * @var string
     */
    public const ENTITIES_PAGE_PATH = '/dashboard/system/express/entities';

    /**
     * @var \Concrete\Core\Navigation\Breadcrumb\Dashboard\DashboardBreadcrumbFactory
     */
    protected $breadcrumbFactory;

    /**
     * @var \Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface
     */
    protected $urlResolver;

    public function __construct(DashboardBreadcrumbFactory $breadcrumbFactory, ResolverManagerInterface $urlResolver)
    {
        $this->breadcrumbFactory = $breadcrumbFactory;
        $this->urlResolver = $urlResolver;
    }

    /**
     * @param \Concrete\Core\Page\Page $dashboardPage the entities page, or one of its child pages
     * @param string $actionName the name of the last item (empty string for none)
     */
    public function getBreadcrumb(Page $dashboardPage, Entity $entity, string $actionName = ''): BreadcrumbInterface
    {
        $breadcrumb = $this->breadcrumbFactory->getBreadcrumb($dashboardPage);
        $items = $breadcrumb->getItems();
        $pagePath = (string) $dashboardPage->getCollectionPath();
        $pageItem = null;
        if ($pagePath !== static::ENTITIES_PAGE_PATH) {
            // Child pages need the entity ID in their URL
            $pageItem = new Item(
                (string) $this->urlResolver->resolve([$pagePath, $entity->getId()]),
                array_pop($items)->getName()
            );
        }
        $items[] = new Item(
            (string) $this->urlResolver->resolve([static::ENTITIES_PAGE_PATH, 'view_entity', $entity->getId()]),
            $entity->getEntityDisplayName('text')
        );
        if ($pageItem !== null) {
            $items[] = $pageItem;
        }
        if ($actionName !== '') {
            $items[] = new Item('', $actionName);
        }

        return $breadcrumb->setItems($items);
    }
}
