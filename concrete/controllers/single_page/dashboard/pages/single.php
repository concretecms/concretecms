<?php

namespace Concrete\Controller\SinglePage\Dashboard\Pages;

use Concrete\Core\Http\Request;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Permission\Checker;

class Single extends DashboardPageController
{
    public function single_page_added()
    {
        $this->set('message', t('Page Successfully Added.'));
        $this->view();
    }

    public function view()
    {
        $this->set('generated', SinglePage::getList());
        if ($this->request->isMethod(Request::METHOD_POST)) {
            if ($this->token->validate('add_single_page')) {
                $pathToNode = SinglePage::getPathToNode($this->request->request->get('pageURL'), false);
                $path = SinglePage::sanitizePath($this->request->request->get('pageURL'));
                if (strlen($pathToNode) > 0) {
                    // now we check to see if this is already added
                    $pc = Page::getByPath('/' . $path, 'RECENT');
                    if ($pc->getError() == COLLECTION_NOT_FOUND) {
                        SinglePage::add(h($this->request->request->get('pageURL')));

                        return $this->buildRedirect($this->action('single_page_added'));
                    }

                    $this->error->add(t('That page has already been added.'));
                } else {
                    $this->error->add(t('That specified path doesn\'t appear to be a valid static page.'));
                }
            }
        }
    }

    public function single_page_refreshed()
    {
        $this->set('message', t('Page Successfully Refreshed.'));
        $this->view();
    }

    public function refresh($cID = 0, $token = '')
    {
        if ((int) $cID > 0) {
            if ($this->token->validate('refresh', $token)) {
                $p = SinglePage::getByID($cID);
                $cp = new Checker($p);
                if ($cp->canAdmin()) {
                    SinglePage::refresh($p);

                    return $this->buildRedirect($this->action('single_page_refreshed'));
                }

                $this->error->add(t('You do not have permissions to refresh this page.'));
            } else {
                $this->error->add($this->token->getErrorMessage());
            }
        } else {
            $this->error->add(t('Page Unsuccessfully Refreshed.'));
        }
    }
}
