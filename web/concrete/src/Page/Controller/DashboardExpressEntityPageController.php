<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Controller\Element\Dashboard\Express\Entries\Header;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Form\Control\SaveHandler\SaveHandlerInterface;
use Concrete\Core\Express\Form\Validator;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\Category;
use Concrete\Core\Tree\Type\ExpressEntryResults;

abstract class DashboardExpressEntityPageController extends DashboardExpressEntriesPageController
{

    protected function getEntity()
    {
        if (!method_exists($this, 'getEntityName')) {
            throw new \RuntimeException(t('Unless you override getEntity() you must define a method named getEntityName'));
        } else {
            return $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entity')
                ->findOneByName($this->getEntityName());
        }
    }

    protected function getResultsTreeNodeObject()
    {
        return Node::getByID($this->getEntity()->getEntityResultsNodeId());
    }

    public function view($folder = null)
    {
        $header = new Header($this->getEntity(), $this->getPageObject());
        $this->renderList($folder);
        $this->set('headerMenu', $header);
    }


    public function create_entry($id = null)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $entity = $r->findOneById($id);
        if (!is_object($entity)) {
            $this->redirect('/dashboard/express/entries');
        }
        $this->set('entity', $entity);
        $form = $entity->getForms()[0];
        $renderer = \Core::make('Concrete\Core\Express\Form\Renderer');
        $this->set('expressForm', $form);
        $this->set('renderer', $renderer);
        $this->set('backURL', $this->getBackToListURL($entity));
        $this->render('/dashboard/express/entries/create', false);
    }

    public function submit($id = null)
    {
        $this->view($id);
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Form');
        $form = $r->findOneById($this->request->request->get('express_form_id'));
        if (is_object($form)) {
            $validator = new Validator($this->error, $this->request);
            $validator->validate($form);
            if (!$this->error->has()) {
                $entry = new Entry();
                $entry->setEntity($this->get('entity'));

                $this->entityManager->persist($entry);
                foreach ($form->getControls() as $control) {
                    $type = $control->getControlType();
                    $saver = $type->getSaveHandler($control);
                    if ($saver instanceof SaveHandlerInterface) {
                        $saver->saveFromRequest($control, $entry, $this->request);
                    }
                }

                $this->entityManager->flush();

                $this->flash('success', t('%s added successfully.', $this->get('entity')->getName()));
                $this->redirect($this->getBackToListURL($this->get('entity')));
            }
        } else {
            throw new \Exception(t('Invalid form.'));
        }

    }
}
