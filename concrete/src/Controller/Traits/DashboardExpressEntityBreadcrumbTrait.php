<?php

declare(strict_types=1);

namespace Concrete\Core\Controller\Traits;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Navigation\Breadcrumb\BreadcrumbInterface;
use Concrete\Core\Navigation\Breadcrumb\Dashboard\DashboardExpressEntityBreadcrumbFactory;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Adds the Express entity to the breadcrumb of the dashboard pages that set the "entity" variable.
 */
trait DashboardExpressEntityBreadcrumbTrait
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Page\Controller\DashboardPageController::createBreadcrumb()
     */
    protected function createBreadcrumb(): BreadcrumbInterface
    {
        $entity = $this->get('entity');
        if (!$entity instanceof Entity) {
            return parent::createBreadcrumb();
        }

        return $this->app->make(DashboardExpressEntityBreadcrumbFactory::class)->getBreadcrumb(
            $this->getPageObject(),
            $entity,
            $this->getEntityBreadcrumbActionName()
        );
    }

    /**
     * Get the name of the last breadcrumb item (empty string for none).
     *
     * By default it's the page title, if it's different from the page name.
     */
    protected function getEntityBreadcrumbActionName(): string
    {
        $pageTitle = (string) $this->get('pageTitle');

        return $pageTitle === t($this->getPageObject()->getCollectionName()) ? '' : $pageTitle;
    }
}
