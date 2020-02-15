<?php

namespace Concrete\Core\StyleCustomizer;

use Concrete\Core\Entity\StyleCustomizer\CustomCssRecord as CustomCssRecordEntity;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;

class CustomCssRecord
{
    /**
     * Get a CustomCssRecord entity given its ID.
     *
     * @param int|mixed $id
     *
     * @return \Concrete\Core\Entity\StyleCustomizer\CustomCssRecord|null
     */
    public static function getByID($id)
    {
        if (!$id) {
            return null;
        }
        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManagerInterface::class);

        return $em->find(CustomCssRecordEntity::class, $id);
    }
}
