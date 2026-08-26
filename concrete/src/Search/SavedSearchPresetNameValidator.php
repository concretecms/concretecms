<?php

declare(strict_types=1);

namespace Concrete\Core\Search;

use Concrete\Core\Entity\Search\SavedExpressSearch;
use Concrete\Core\Entity\Search\SavedSearch;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class SavedSearchPresetNameValidator
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function isUnique(SavedSearch $search, string $presetName): bool
    {
        $criteria = ['presetName' => $presetName];
        if ($search instanceof SavedExpressSearch) {
            $criteria['entity'] = $search->getEntity();
        }

        $currentID = $search->getID();
        $repository = $this->entityManager->getRepository(ClassUtils::getClass($search));
        foreach ($repository->findBy($criteria) as $existingSearch) {
            if ($currentID === null || (string) $existingSearch->getID() !== (string) $currentID) {
                return false;
            }
        }

        return true;
    }
}
