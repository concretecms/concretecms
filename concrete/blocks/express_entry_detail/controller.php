<?php

namespace Concrete\Block\ExpressEntryDetail;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Express\Form\Context\FrontendViewContext;
use Concrete\Core\Express\Form\Renderer;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Form\Context\ContextFactory;
use Concrete\Core\Html\Service\Seo;
use Concrete\Core\Support\Facade\Express;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Url\SeoCanonical;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class Controller extends BlockController implements UsesFeatureInterface
{
    protected $btInterfaceWidth = '640';

    protected $btInterfaceHeight = '400';

    protected $btTable = 'btExpressEntryDetail';

    public $exEntityID;

    public $exSpecificEntryID;

    public $exEntryAttributeKeyHandle;

    public $exFormID;

    public $entryMode;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    public function on_start()
    {
        parent::on_start();
        $this->app = Facade::getFacadeApplication();
        $this->entityManager = $this->app->make('database/orm')->entityManager();
    }

    public function getBlockTypeDescription()
    {
        return t('Add an Express entry detail display to a page.');
    }

    public function getBlockTypeName()
    {
        return t('Express Entry Detail');
    }

    public function getBlockTypeInSetName()
    {
        return t('Details');
    }

    public function getRequiredFeatures(): array
    {
        return [
            Features::EXPRESS,
        ];
    }

    public function add()
    {
        $this->loadData();
        $this->set('entryMode', 'L');
        $this->set('exEntityID', null);
        $this->set('entity', null);
        $this->set('exEntryAttributeKeyHandle', null);
        $this->set('exFormID', null);
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
        $this->set('exEntryAttributeKeyHandle', $this->exEntryAttributeKeyHandle ?? null);
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

        $form = null;
        try {
            $form = $this->entityManager->find('Concrete\Core\Entity\Express\Form', $this->exFormID);
        } catch (Exception $e) {
            $logger = $this->app->make('log/exceptions');
            $logger->addEmergency($e->getMessage());
        }

        if ($form && $entity) {
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
                /** @var Seo $seo */
                $seo = $this->app->make('helper/seo');
                $seo->addTitleSegmentBefore($entry->getLabel());

                /** @var SeoCanonical $canonical */
                $canonical = $this->app->make(SeoCanonical::class);
                $canonical->setPathArguments(['view_express_entity', $exEntryID]);

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
                $r = new \stdClass();
                $r->forms = [];
                foreach ($entity->getForms() as $form) {
                    $r->forms[] = $form;
                }
                ob_start();
                $form_selector = $this->app->make('form/express/entry_selector');
                echo $form_selector->selectEntry($entity, 'exSpecificEntryID');
                $r->selector = ob_get_contents();
                ob_end_clean();

                return new JsonResponse($r);
            }
        }

        \Core::make('app')->shutdown();
    }

    public function loadData()
    {
        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entity');
        $entityObjects = $r->findAll();
        $entities = ['' => t('** Choose Entity')];
        foreach ($entityObjects as $entity) {
            $entities[$entity->getID()] = $entity->getEntityDisplayName();
        }
        $this->set('entities', $entities);
        $attributeKeys = [];
        $keys = CollectionKey::getList();
        foreach ($keys as $ak) {
            if ($ak->getAttributeTypeHandle() == 'express') {
                $attributeKeys[] = $ak;
            }
        }
        $this->set('expressAttributes', $attributeKeys);
    }
}
