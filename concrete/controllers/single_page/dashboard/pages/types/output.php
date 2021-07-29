<?php

namespace Concrete\Controller\SinglePage\Dashboard\Pages\Types;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Url\Resolver\Manager\ResolverManager;

class Output extends DashboardPageController
{
    /** @var \Concrete\Core\Page\Type\Type */
    private $pagetype;

    /**
     * The main view method to display the available page templates
     * for the passed page type.
     *
     * @param mixed $ptID the id of the page type
     *
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | void
     */
    public function view($ptID = false)
    {
        $this->pagetype = Type::getByID($ptID);
        if (!$this->pagetype) {
            return $this->buildRedirect('/dashboard/pages/types');
        }
        $cmp = new Checker($this->pagetype);
        if (!$cmp->canEditPageType()) {
            throw new UserMessageException(t('You do not have access to edit this page type.'));
        }
        $this->set('pagetype', $this->pagetype);
    }

    /**
     * This function mainly redirects to the master collection for the
     * given page type and page template.
     *
     * @param mixed $ptID the id of the page type
     * @param mixed $pTemplateID the id of the page template
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | void
     */
    public function edit_defaults($ptID = false, $pTemplateID = false)
    {
        $this->view($ptID);
        $template = Template::getByID($pTemplateID);
        if (!is_object($template)) {
            return $this->buildRedirect('/dashboard/pages/types');
        }
        $valid = false;
        foreach ($this->pagetype->getPageTypePageTemplateObjects() as $pt) {
            if ($pt->getPageTemplateID() == $template->getPageTemplateID()) {
                $valid = true;
                break;
            }
        }
        if (!$valid) {
            $this->error->add(t('Invalid page template.'));
        }
        if (!$this->error->has()) {
            // we load up the master template for this composer/template combination.
            $c = $this->pagetype->getPageTypePageTemplateDefaultPageObject($template);
            $this->app->make('session')->set('mcEditID', $c->getCollectionID());

            return $this->buildRedirect($this->app->make(ResolverManager::class)->resolve([$c]))->send();
        }
    }
}
