<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Entity\Page\Theme\CustomSkin;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Utility\Service\Text;
use Doctrine\ORM\EntityManager;

class SkinCommandValidator
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Text
     */
    protected $textService;

    public function __construct(EntityManager $entityManager, Text $textService)
    {
        $this->entityManager = $entityManager;
        $this->textService = $textService;
    }

    public function validate(string $skinName, Theme $theme): ErrorList
    {
        $error = new ErrorList();

        $skinIdentifier = $this->textService->urlify(trim($skinName));
        if (!$skinIdentifier) {
            $error->add(t('Unable to generate skin identifier from name.'));
        } else {

            $skins = $theme->getSkins();
            foreach ($skins as $skin) {
                if ($skin->getIdentifier() == $skinIdentifier) {
                    $error->add(t('There is already a preset skin with the identifier "%s"', $skinIdentifier));
                }
            }

            $skin = $this->entityManager->getRepository(CustomSkin::class)
                ->findOneBySkinIdentifier($skinIdentifier);
            if ($skin) {
                $error->add(t('There is already a custom skin with the identifier "%s"', $skinIdentifier));
            }
        }
        return $error;
    }


}
