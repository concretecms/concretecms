<?php
namespace Concrete\Core\Entity\Attribute\Value;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="SiteTypeAttributeValues"
 * )
 */
class SiteTypeValue extends AbstractValue
{
    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Site\Skeleton")
     * @ORM\JoinColumn(name="siteSkeletonID", referencedColumnName="siteSkeletonID")
     */
    protected $skeleton;

    /**
     * @return mixed
     */
    public function getSkeleton()
    {
        return $this->skeleton;
    }

    /**
     * @param mixed $skeleton
     */
    public function setSkeleton($skeleton)
    {
        $this->skeleton = $skeleton;
    }



}
