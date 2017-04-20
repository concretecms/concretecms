<?php
namespace Concrete\Block\ExpressEntryDetail;

use Concrete\Controller\Element\Search\CustomizeResults;
use Concrete\Core\Attribute\Key\CollectionKey;
use \Concrete\Core\Block\BlockController;
use Concrete\Core\Express\Entry\Search\Result\Result;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\Form\Context\FrontendViewContext;
use Concrete\Core\Express\Form\Renderer;
use Concrete\Core\Form\Context\ContextFactory;
use Concrete\Core\Search\Result\ItemColumn;
use Concrete\Core\Support\Facade\Express;
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
                $this->set('entry', Express::getEntry($this->exSpecificEntryID));
            }
        }

    }

    public function view()
    {
        $c = \Page::getCurrentPage();
        $entity = null;
        if ($this->entryMode == 'A') {
            $ak = CollectionKey::getByHandle($this->exEntryAttributeKeyHandle);
            if (is_object($ak)) {
                $settings = $ak->getAttributeKeySettings();
                $value = $c->getAttribute($ak);
                if (is_object($settings)) {
                    $entity = $settings->getEntity();
                    $this->set('entity', $settings->getEntity());
                }
                if (is_object($value)) {
                    $this->set('entry', $value->getSelectedEntries()[0]);
                }
            }
        } else {
            $entity = $this->entityManager->find('Concrete\Core\Entity\Express\Entity', $this->exEntityID);
            if (is_object($entity)) {
                $this->set('entity', $entity);

                if ($this->entryMode == 'S' && $this->exSpecificEntryID) {
                    $this->set('entry', Express::getEntry($this->exSpecificEntryID));
                }

            }
        }
        $form = $this->entityManager->find('Concrete\Core\Entity\Express\Form', $this->exFormID);

        if ($form) {
            $express = \Core::make('express');
            $controller = $express->getEntityController($entity);
            $factory = new ContextFactory($controller);
            $context = $factory->getContext(new FrontendViewContext());
            $renderer = new Renderer(
                $context,
                $form
            );

            $this->set('renderer', $renderer);
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
            $entities[$entity->getID()] = $entity->getEntityDisplayName();
        }
        $this->set('entities', $entities);
        $keys = CollectionKey::getList();
        foreach ($keys as $ak) {
            if ($ak->getAttributeTypeHandle() == 'express') {
                $attributeKeys[] = $ak;
            }
        }
        $this->set('expressAttributes', $attributeKeys);
    }

}
