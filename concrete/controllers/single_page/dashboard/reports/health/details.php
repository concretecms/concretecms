<?php
namespace Concrete\Controller\SinglePage\Dashboard\Reports\Health;

use Concrete\Core\Entity\Health\Report\Result;
use Concrete\Core\Health\Report\Command\DeleteReportResultCommand;
use Concrete\Core\Navigation\Item\Item;
use Concrete\Core\Navigation\Item\PageItem;
use Concrete\Core\Page\Controller\DashboardPageController;

class Details extends DashboardPageController
{

    private function setHealthResultBreadcrumb(Result $result)
    {
        $breadcrumb = $this->createBreadcrumb();
        $items = $breadcrumb->getItems();
        array_pop($items);
        $breadcrumb->setItems($items);
        $breadcrumb->add(new Item('#', $result->getName()));
        $this->setBreadcrumb($breadcrumb);
    }

    public function view($resultId = null)
    {
        $result = null;
        if ($resultId) {
            $result = $this->entityManager->find(Result::class, $resultId);
        }

        if (!$result) {
            throw new \Exception(t('Invalid result ID.'));
        }

        // Set breadcrumb
        $this->setHealthResultBreadcrumb($result);

        $this->set('result', $result);
    }

    public function delete()
    {
        $this->view($this->request->request->get('resultID'));
        if (!$this->token->validate('delete')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $result = $this->get('result');

            $command = new DeleteReportResultCommand($result->getId());
            $this->app->executeCommand($command);

            $this->flash('success', t('Result deleted successfully.'));
            return $this->buildRedirect(['/dashboard/reports/health']);
        }
    }



}
