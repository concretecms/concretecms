<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards;

use Concrete\Core\Board\Designer\Command\CreateItemSelectorCustomElementCommand;
use Concrete\Core\Entity\Board\Designer\CustomElement;
use Concrete\Core\Page\Controller\DashboardPageController;

class Designer extends DashboardPageController
{

    public function view()
    {
        $r = $this->entityManager->getRepository(CustomElement::class);
        $elements = $r->findBy(['status' => CustomElement::STATUS_READY_TO_PUBLISH], ['dateCreated' => 'desc']);
        $drafts = $r->findBy(['status' => CustomElement::STATUS_DRAFT], ['dateCreated' => 'desc']);
        $this->set('elements', $elements);
        $this->set('drafts', $drafts);
    }

    public function view_element($id = null)
    {
        $element = $this->entityManager->find(CustomElement::class, $id);
        if ($element && $element->getStatus() == CustomElement::STATUS_READY_TO_PUBLISH) {
            $this->set('element', $element);
            $this->render('/dashboard/boards/designer/view_element');
        }
    }

    public function add()
    {
        $this->render('/dashboard/boards/designer/add');
    }

    public function delete_element($id)
    {
        if (!$this->token->validate('delete_element')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $element = $this->entityManager->find(CustomElement::class, $id);
            if ($element) {
                $this->entityManager->remove($element);
                $this->entityManager->flush();
                $this->flash('success', t('Custom element removed.'));
                return $this->buildRedirect(['/dashboard/boards/designer']);
            }
        }
    }

    public function getContinueURL(CustomElement $element)
    {
        $url = $this->app->make('url');
        /*
        $items = $element->getItems();
        if (count($items)) {
            if ($element->getSlotTemplate()) {
                return $url->to('/dashboard/boards/designer/publish', $element->getID());
            } else {
                return $url->to('/dashboard/boards/designer/customize_slot', $element->getID());
            }
        } else {
            return $url->to('/dashboard/boards/designer/choose_items', $element->getID());
        }*/
        // The logic above is nice but it's also nice to be able to change the selected items.
        return $url->to('/dashboard/boards/designer/choose_items', $element->getID());
    }

    public function add_element()
    {
        if (!$this->token->validate('add_element')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->request->request->get('elementName')) {
            $this->error->add(t('Element name is required.'));
        }
        if (!$this->error->has()) {
            // This is where we'll eventually put a switch on the creation method. But since there's only one
            // creation method at the moment that would be unnecessary.
            $command = new CreateItemSelectorCustomElementCommand();
            $command->setElementName($this->request->request->get('elementName'));
            $element = $this->app->executeCommand($command);
            return $this->buildRedirect(['/dashboard/boards/designer/choose_items', $element->getID()]);
        }
        $this->add();
    }

}
