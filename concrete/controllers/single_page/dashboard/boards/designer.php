<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards;

use Concrete\Core\Board\Designer\Command\CreateCustomElementCommand;
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
            $command = new CreateCustomElementCommand();
            $command->setCreationMethod($this->request->request->get('creationMethod'));
            $command->setElementName($this->request->request->get('elementName'));
            $element = $this->app->executeCommand($command);
            return $this->buildRedirect(['/dashboard/boards/designer/choose_items', $element->getID()]);
        }
    }

}
