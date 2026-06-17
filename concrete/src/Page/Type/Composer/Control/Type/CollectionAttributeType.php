<?php

declare(strict_types=1);

namespace Concrete\Core\Page\Type\Composer\Control\Type;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\Key\Key as AttributeKey;
use Concrete\Core\Page\Type\Composer\Control\CollectionAttributeControl;

defined('C5_EXECUTE') or die('Access Denied.');

class CollectionAttributeType extends Type
{
    /**
     * @var CategoryService
     */
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    // Satisfies the abstract contract in Type. Called by the flat-render path in the picker view
    // for any control type that is not a CollectionAttributeType. Do not delegate to
    // getPageTypeComposerControlObjectsBySet() here — third-party types that override this method
    // for custom filtering would be silently bypassed.
    public function getPageTypeComposerControlObjects()
    {
        $objects = [];

        foreach (AttributeKey::getAttributeKeyList('collection') as $ak) {
            $objects[] = $this->makeControlForKey($ak);
        }

        return $objects;
    }

    /**
     * Returns controls grouped by attribute set for use in the picker dialog.
     * Sets that contain only internal attributes (akIsInternal=true) are omitted entirely.
     * Set ordering respects the admin-configured asDisplayOrder. Unassigned keys appear last.
     *
     * @return array{sets: array<array{set: \Concrete\Core\Entity\Attribute\Set, controls: CollectionAttributeControl[]}>, unassigned: CollectionAttributeControl[]}
     */
    public function getPageTypeComposerControlObjectsBySet()
    {
        $category = $this->categoryService->getByHandle('collection');
        $controller = $category->getController();
        $setManager = $controller->getSetManager();

        // getList() applies akIsInternal=false; $set->getAttributeKeys() does not,
        // so intersect against this whitelist to avoid surfacing internal keys.
        $whitelist = [];
        foreach ($controller->getList() as $key) {
            $whitelist[$key->getAttributeKeyID()] = $key;
        }

        $sets = [];
        foreach ($setManager->getAttributeSets() as $set) {
            $controls = [];
            foreach ($set->getAttributeKeys() as $key) {
                if (isset($whitelist[$key->getAttributeKeyID()])) {
                    $controls[] = $this->makeControlForKey($whitelist[$key->getAttributeKeyID()]);
                }
            }
            if (!empty($controls)) {
                $sets[] = ['set' => $set, 'controls' => $controls];
            }
        }

        $unassigned = [];
        foreach ($setManager->getUnassignedAttributeKeys() as $key) {
            $unassigned[] = $this->makeControlForKey($key);
        }

        return ['sets' => $sets, 'unassigned' => $unassigned];
    }

    public function getPageTypeComposerControlByIdentifier($identifier)
    {
        return $this->makeControlForKey(\CollectionAttributeKey::getByID($identifier));
    }

    public function configureFromImportHandle($handle)
    {
        $ak = \CollectionAttributeKey::getByHandle($handle);
        if (!$ak) {
            throw new \Exception(t('Import error: Unable to locate attribute for handle: %s', $handle));
        }

        return static::getPageTypeComposerControlByIdentifier($ak->getAttributeKeyID());
    }

    /**
     * @param \Concrete\Core\Entity\Attribute\Key\Key|\CollectionAttributeKey $key
     */
    private function makeControlForKey($key): CollectionAttributeControl
    {
        $ac = new CollectionAttributeControl();
        $ac->setAttributeKeyID($key->getAttributeKeyID());
        $ac->setPageTypeComposerControlIconFormatter($key->getController()->getIconFormatter());
        $ac->setPageTypeComposerControlName($key->getAttributeKeyDisplayName());

        return $ac;
    }
}
