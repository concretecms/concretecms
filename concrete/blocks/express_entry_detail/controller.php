<?php

namespace Concrete\Block\ExpressEntryDetail;

use Concrete\Core\Api\ApiValueSchemaInterface;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Express\Form\Context\FrontendViewContext;
use Concrete\Core\Express\Form\Renderer;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Form\Context\ContextFactory;
use Concrete\Core\Html\Service\Seo;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Support\Facade\Express;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Url\SeoCanonical;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use SimpleXMLElement;
use Symfony\Component\HttpFoundation\JsonResponse;

class Controller extends BlockController implements ApiValueSchemaInterface, UsesFeatureInterface
{
    /**
     * Value of the entryMode column: the entry is the one picked by the list block of another page.
     *
     * @var string
     */
    protected const ENTRY_MODE_FROM_LIST_BLOCK = 'E';

    /**
     * Value of the entryMode column: the entry is the one held by the exSpecificEntryID column.
     *
     * @var string
     */
    protected const ENTRY_MODE_SPECIFIC_ENTRY = 'S';

    /**
     * Value of the entryMode column: the entry is the one held by the Express attribute (whose handle is in
     * the exEntryAttributeKeyHandle column) of the page holding the block.
     *
     * @var string
     */
    protected const ENTRY_MODE_PAGE_ATTRIBUTE = 'A';

    protected $btInterfaceWidth = '640';

    protected $btInterfaceHeight = '400';

    protected $btTable = 'btExpressEntryDetail';

    protected $btCacheBlockOutput = null;

    protected $btCacheBlockOutputLifetime = 300;

    public $exEntityID;

    public $exSpecificEntryID;

    public $exEntryAttributeKeyHandle;

    public $exFormID;

    public $entryMode;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    public function on_start()
    {
        parent::on_start();
        $this->app = Facade::getFacadeApplication();
        $this->entityManager = $this->app->make(EntityManagerInterface::class);
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
        $this->set('entryMode', '');
        $this->set('entryModes', $this->getAvailableEntryModes());
        $this->set('exEntityID', null);
        $this->set('entity', null);
        $this->set('exEntryAttributeKeyHandle', null);
        $this->set('exFormID', null);
    }

    public function edit()
    {
        $this->loadData();
        if ($this->exEntityID) {
            $entity = $this->entityManager->find(Entity::class, $this->exEntityID);
            if (is_object($entity)) {
                $this->set('entity', $entity);
            }
            if ($this->exSpecificEntryID) {
                $this->set('entry', Express::getEntry($this->exSpecificEntryID));
            }
        }
        $this->set('exEntryAttributeKeyHandle', $this->exEntryAttributeKeyHandle ?? null);
        $this->set('entryModes', $this->getAvailableEntryModes());
    }

    public function view()
    {
        $c = $this->getCollectionObject();
        $entity = null;
        if ($this->entryMode == self::ENTRY_MODE_PAGE_ATTRIBUTE) {
            $ak = CollectionKey::getByHandle($this->exEntryAttributeKeyHandle);
            if (is_object($ak) && is_object($c)) {
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
            $entity = $this->entityManager->find(Entity::class, $this->exEntityID);
            if (is_object($entity)) {
                $this->set('entity', $entity);

                if ($this->entryMode == self::ENTRY_MODE_SPECIFIC_ENTRY && $this->exSpecificEntryID) {
                    $this->set('entry', Express::getEntry($this->exSpecificEntryID));
                }
            }
        }

        $form = null;
        try {
            $form = $this->entityManager->find(Form::class, $this->exFormID);
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

    /**
     * @param int|string|null $exEntryID
     */
    public function action_view_express_entity($exEntryID = null)
    {
        $objectManager = $this->app->make(ObjectManager::class);
        $entry = $exEntryID ? $objectManager->getEntryByPublicIdentifier($exEntryID) : null;
        if ($entry) {
            $entity = $this->entityManager->find(Entity::class, $this->exEntityID);
            $checker = new Checker($entity);
            if (!$checker->canViewExpressEntries()) {
                throw new UserMessageException(t('Access Denied.'));
            }
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

    public function getSearchableContent()
    {
        // The entity manager doesn't appear to be available in the CLI version of task running? At least not
        // at the block level when retrieving searchable content? So let's manually create it.
        if (!isset($this->entityManager)) {
            $this->entityManager = $this->app->make(EntityManagerInterface::class);
        }
        // Let's run the view() method so we can populate the renderer object and the entry that we're supposed
        // to render.
        $this->view();

        // Now, if we do indeed have the renderer and entry defined within the block controller, let's retrieve them.
        if (($renderer = $this->get('renderer')) && ($entry = $this->get('entry'))) {

            // Since we have the entry and renderer, let's actually render the thing. This will result in printing
            // out actual HTML, so let's output-buffer it.
            ob_start();
            $renderer->render($entry);
            $content = ob_get_contents();
            ob_end_clean();

            // Finally, let's do some quirk and dirty removal of HTML and the massive newlines that
            // such an operation will create. NOTE: This is not meant to be perfect or meant to look
            // particularly beautiful if rendered - it is meant to populate the search index.
            $content = str_replace(["\r", "\n"], " ", strip_tags($content));
            return $content;
        }
        return '';
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

    /**
     * Get the places the entry to be displayed can come from.
     *
     * @return array<string,string> the keys are the values of the entryMode column, the values are their names
     */
    protected function getAvailableEntryModes(): array
    {
        return [
            self::ENTRY_MODE_FROM_LIST_BLOCK => t('Get entry from list block on another page'),
            self::ENTRY_MODE_SPECIFIC_ENTRY => t('Display specific entry'),
            self::ENTRY_MODE_PAGE_ATTRIBUTE => t('Get entry from custom attribute on this page'),
        ];
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

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\ApiValueSchemaInterface::getApiValueSchema()
     */
    public function getApiValueSchema(): array
    {
        return [
            'type' => 'object',
            'description' => 'The Express entry displayed by this block, and the form that renders it.',
            'properties' => [
                'entryMode' => [
                    'type' => 'string',
                    'enum' => array_keys($this->getAvailableEntryModes()),
                    'default' => self::ENTRY_MODE_SPECIFIC_ENTRY,
                    'description' => 'Where the entry to be displayed comes from: "' . self::ENTRY_MODE_FROM_LIST_BLOCK . '" the list block of another page, "' . self::ENTRY_MODE_SPECIFIC_ENTRY . '" the exSpecificEntryID entry, "' . self::ENTRY_MODE_PAGE_ATTRIBUTE . '" the exEntryAttributeKeyHandle attribute of the page holding this block.',
                ],
                'exEntityID' => [
                    'type' => ['string', 'null'],
                    'description' => 'The ID of the Express entity the entry belongs to (it\'s not used when entryMode is "' . self::ENTRY_MODE_PAGE_ATTRIBUTE . '", since the attribute knows the entity).',
                ],
                'exSpecificEntryID' => [
                    'type' => ['string', 'integer', 'null'],
                    'description' => 'The ID of the entry to be displayed (it\'s used only when entryMode is "' . self::ENTRY_MODE_SPECIFIC_ENTRY . '").',
                ],
                'exEntryAttributeKeyHandle' => [
                    'type' => ['string', 'null'],
                    'description' => 'The handle of the Express attribute of the page holding the entry to be displayed (it\'s used only when entryMode is "' . self::ENTRY_MODE_PAGE_ATTRIBUTE . '").',
                ],
                'exFormID' => [
                    'type' => ['string', 'null'],
                    'description' => 'The ID of the form of the Express entity that renders the entry.',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::export()
     */
    public function export(SimpleXMLElement $blockNode)
    {
        parent::export($blockNode);
        $entityIDNode = $blockNode[0]->data[0]->record[0]->exEntityID;
        $entityID = (string) $entityIDNode;
        if ($entityID !== '') {
            $entity = $this->app->make(EntityManagerInterface::class)->find(Entity::class, $entityID);
            if ($entity !== null) {
                $entityIDNode['handle'] = $entity->getHandle();
            }
        }
        $formIDNode = $blockNode[0]->data[0]->record[0]->exFormID[0];
        $formID = (string) $formIDNode;
        if ($formID !== '') {
            $form = $this->app->make(EntityManagerInterface::class)->find(Form::class, $formID);
            if ($form !== null) {
                $formIDNode['name'] = $form->getName();
                $formIDNode['owner-entity'] = $form->getEntity()->getHandle();
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getImportData()
     */
    protected function getImportData($blockNode, $page)
    {
        $args = parent::getImportData($blockNode, $page);
        $em = $this->app->make(EntityManagerInterface::class);
        $entityID = (string) ($args['exEntityID'] ?? '');
        $entity = null;
        if ($entityID !== '') {
            $entity = $em->find(Entity::class, $entityID);
            if ($entity === null) {
                $entityHandle = (string) $blockNode[0]->data[0]->record[0]->exEntityID[0]['handle'];
                if ($entityHandle !== '') {
                    $entity = $em->getRepository(Entity::class)->findOneBy(['handle' => $entityHandle]);
                    if ($entity !== null) {
                        $args['exEntityID'] = $entity->getId();
                    }
                }
            }
        }
        $formID = (string) ($args['exFormID'] ?? '');
        if ($formID !== '') {
            $form = $em->find(Form::class, $formID);
            if ($form === null) {
                $formName = (string) $blockNode[0]->data[0]->record[0]->exFormID[0]['name'];
                if ($formName !== '') {
                    if ($entity === null) {
                        $entityHandle = (string) $blockNode[0]->data[0]->record[0]->exFormID[0]['owner-entity'];
                        if ($entityHandle !== '') {
                            $entity = $em->getRepository(Entity::class)->findOneBy(['handle' => $entityHandle]);
                        }
                    }
                    if ($entity !== null) {
                        $form = $em->getRepository(Form::class)->findOneBy(['name' => $formName, 'entity' => $entity->getId()]);
                        if ($form !== null) {
                            $args['exFormID'] = $form->getId();
                        }
                    }
                }
            }
        }

        return $args;
    }

    public function cacheBlockOutput()
    {
        if ($this->btCacheBlockOutput === null) {
            if ($this->entryMode !== self::ENTRY_MODE_FROM_LIST_BLOCK) {
                $this->btCacheBlockOutput = true;
            } else {
                $this->btCacheBlockOutput = false;
            }
        }

        return $this->btCacheBlockOutput;
    }
}
