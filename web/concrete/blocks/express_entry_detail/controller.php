<?php
namespace Concrete\Block\ExpressEntryDetail;

use Concrete\Controller\Element\Search\CustomizeResults;
use \Concrete\Core\Block\BlockController;
use Concrete\Core\Express\Entry\Search\Result\Result;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Search\Result\ItemColumn;
use Concrete\Core\Support\Facade\Facade;
use Symfony\Component\HttpFoundation\JsonResponse;

class Controller extends BlockController
{

    protected $btInterfaceWidth = "640";
    protected $btInterfaceHeight = "400";
    protected $btTable = 'btExpressEntryDetail';

    public function on_start()
    {
        parent::on_start();
        $this->app = Facade::getFacadeApplication();
        $this->entityManager = $this->app->make('database/orm')->entityManager();
    }

    public function getBlockTypeDescription()
    {
        return t("Add an Express entry detail display to a page.");
    }

    public function getBlockTypeName()
    {
        return t("Express Entry Detail");
    }

    public function getBlockTypeInSetName()
    {
        return t("Details");
    }

    public function add()
    {
        $this->loadData();
    }

    public function edit()
    {
        $this->loadData();
        if ($this->exEntityID) {
            $entity = $this->entityManager->find('Concrete\Core\Entity\Express\Entity', $this->exEntityID);
            if (is_object($entity)) {
                $this->set('entity', $entity);
            }
            if ($this->exSpecificEntryID) {
                $entry = $this->entityManager->find('Concrete\Core\Entity\Express\Entry', $this->exSpecificEntryID);
                if (is_object($entity)) {
                    $this->set('entry', $entry);
                }
            }
        }

    }

    public function view()
    {
        $entity = $this->entityManager->find('Concrete\Core\Entity\Express\Entity', $this->exEntityID);
        if (is_object($entity)) {
            $this->set('entity', $entity);

            if ($this->entryMode == 'S' && $this->exSpecificEntryID) {
                $entry = $this->entityManager->find('Concrete\Core\Entity\Express\Entry', $this->exSpecificEntryID);
                if (is_object($entity)) {
                    $this->set('entry', $entry);
                }
            }

            $form = $this->entityManager->find('Concrete\Core\Entity\Express\Form', $this->exFormID);
            $renderer = $this->app->make('Concrete\Core\Express\Form\ViewRenderer');
            $this->set('renderer', $renderer);
            $this->set('expressForm', $form);
        }
    }

    public function action_view_express_entity($exEntryID = null)
    {
        $entry = $this->entityManager->find('Concrete\Core\Entity\Express\Entry', $exEntryID);
        if (is_object($entry)) {
            $entity = $this->entityManager->find('Concrete\Core\Entity\Express\Entity', $this->exEntityID);
            if ($entry->getEntity()->getID() == $entity->getID()) {
                $this->set('entry', $entry);
                $this->view();
            }
        }
    }

    public function action_load_entity_data()
    {
        $exEntityID = $this->request->request->get('exEntityID');
        if ($exEntityID) {
            $entity = $this->entityManager->find('Concrete\Core\Entity\Express\Entity', $exEntityID);
            if (is_object($entity)) {
                $r = new \stdClass;
                $r->forms = array();
                foreach($entity->getForms() as $form) {
                    $r->forms[] = $form;
                }
                ob_start();
                $form_selector = $this->app->make('form/express/entry_selector');
                print $form_selector->selectEntry($entity, 'exSpecificEntryID');
                $r->selector = ob_get_contents();
                ob_end_clean();

                return new JsonResponse($r);
            }
        }

        \Core::make('app')->shutdown();
    }


    public function loadData()
    {
        $this->requireAsset('core/express');
        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entity');
        $entityObjects = $r->findAll();
        $entities = array('' => t("** Choose Entity"));
        foreach($entityObjects as $entity) {
            $entities[$entity->getID()] = $entity->getName();
        }
        $this->set('entities', $entities);
    }

}
