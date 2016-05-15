<?php
namespace Concrete\Core\StyleCustomizer;

use Doctrine\ORM\Mapping as ORM;

class CustomCssRecord
{

    public static function getByID($id)
    {
        $em = \ORM::entityManager();
        $r = $em->find('\Concrete\Core\Entity\StyleCustomizer\CustomCssRecord', $id);

        return $r;
    }
}
