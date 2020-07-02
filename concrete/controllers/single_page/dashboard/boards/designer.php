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
        $elements = $r->findBy([], ['dateCreated' => 'desc']);
        $this->set('elements', $elements);
    }

    public function add()
    {
        $this->render('/dashboard/boards/designer/add');
    }

    public function add_element()
    {
        if (!$this->token->validate('add_element')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            // This is where we'll eventually put a switch on the creation method. But since there's only one
            // creation method at the moment that would be unnecessary.
            $command = new CreateItemSelectorCustomElementCommand();
            $command->setElementName($this->request->request->get('elementName'));
            $element = $this->app->executeCommand($command);
            return $this->buildRedirect(['/dashboard/boards/designer/choose_items', $element->getID()]);
        }
    }

}
