<?php

namespace Concrete\Block\ExpressEntryDetail;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Attribute\Key\PageKey;
use Concrete\Core\Express\Form\Context\FrontendViewContext;
use Concrete\Core\Express\Form\Renderer;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Form\Context\ContextFactory;
use Concrete\Core\Html\Service\Seo;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Express;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Url\SeoCanonical;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @var int
     */
    protected $btInterfaceWidth = 640;

    /**
     * @var int
     */
    protected $btInterfaceHeight = 400;

    /**
     * @var string
     */
    protected $btTable = 'btExpressEntryDetail';

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var string|null
     */
    protected $exEntityID;

    /**
     * @var int|null
     */
    protected $exSpecificEntryID;

    /**
     * @var string|null
     */
    protected $entryMode;

    /**
     * @var ObjectManager|null
     */
    protected $objectManager;

    /**
     * @var string|null
     */
    protected $exFormID;

    /**
     * @var string|null
     */
    protected $exEntryAttributeKeyHandle;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function on_start()
    {
        parent::on_start();
        $this->app = Facade::getFacadeApplication();
        $this->entityManager = $this->app->make('database/orm')->entityManager();
    }

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Add an Express entry detail display to a page.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Express Entry Detail');
    }

    /**
     * @return string
     */
    public function getBlockTypeInSetName()
    {
        return t('Details');
    }

    /**
     * @return string[]
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::EXPRESS,
        ];
    }

    /**
     * @return void
     */
    public function add()
    {
        $this->loadData();
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     *
     * @return void
     */
    public function edit()
    {
        $this->loadData();
        if ($this->exEntityID) {
            /** @var \Concrete\Core\Entity\Express\Entity|null $entity */
            $entity = $this->entityManager->find('Concrete\Core\Entity\Express\Entity', $this->exEntityID);
            if (is_object($entity)) {
                $this->set('entity', $entity);
            }
            if ($this->exSpecificEntryID) {
                $this->set('entry', $this->getObjectManager()->getEntry($this->exSpecificEntryID));
            }
        }
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function view()
    {
        $c = Page::getCurrentPage();
        $entity = null;
        if ($this->entryMode === 'A') {
            /** @var PageKey|null $ak */
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
            /** @var \Concrete\Core\Entity\Express\Entity|null $entity */
            $entity = $this->entityManager->find('Concrete\Core\Entity\Express\Entity', $this->exEntityID);
            if (is_object($entity)) {
                $this->set('entity', $entity);

                if ($this->entryMode === 'S' && $this->exSpecificEntryID) {
                    $this->set('entry', $this->getObjectManager()->getEntry($this->exSpecificEntryID));
                }
            }
        }

        $form = null;
        try {
            /** @var \Concrete\Core\Entity\Express\Form|null $form */
            $form = $this->entityManager->find('Concrete\Core\Entity\Express\Form', $this->exFormID);
        } catch (Exception $e) {
            $logger = $this->app->make('log/exceptions');
            $logger->addEmergency($e->getMessage());
        }

        if ($form) {
            $express = $this->getObjectManager();
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

    /**
     * @param string|null $exEntryID guid of an express entry
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function action_view_express_entity($exEntryID = null)
    {
        /** @var \Concrete\Core\Entity\Express\Entry|null $entry */
        $entry = $this->entityManager->find('Concrete\Core\Entity\Express\Entry', $exEntryID);
        if (is_object($entry)) {
            /** @var \Concrete\Core\Entity\Express\Entity|null $entity */
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

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return JsonResponse|void
     */
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

        $this->app->shutdown();
    }

    /**
     * @return void
     */
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
        /** @var PageKey[] $keys */
        /** @phpstan-ignore-next-line */
        $keys = CollectionKey::getList();
        foreach ($keys as $ak) {
            if ($ak->getAttributeTypeHandle() === 'express') {
                $attributeKeys[] = $ak;
            }
        }
        $this->set('expressAttributes', $attributeKeys);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function getObjectManager(): ObjectManager
    {
        if (!$this->objectManager) {
            $this->objectManager = $this->app->make(ObjectManager::class);
        }

        return $this->objectManager;
    }
}
