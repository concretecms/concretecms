<?php
namespace Concrete\Core\Entity\User;

use Doctrine\ORM\EntityRepository;

/**
 * @extends \Doctrine\ORM\EntityRepository<\Concrete\Core\Entity\Attribute\Key\UserKey>
 */
class AttributeRepository extends EntityRepository
{
    public function getRegistrationList()
    {
        return $this->findBy(
            array('uakRegisterEdit' => true)
        );
    }

    public function getMemberListList()
    {
        return $this->findBy(
            array('uakMemberListDisplay' => true)
        );
    }

    public function getPublicProfileList()
    {
        return $this->findBy(
            array('uakProfileDisplay' => true)
        );
    }

    public function getEditableInProfileList()
    {
        return $this->findBy(
            array('uakProfileEdit' => true)
        );
    }

}
