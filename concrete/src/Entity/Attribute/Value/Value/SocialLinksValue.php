<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SocialLinkAttributeValues")
 */
class SocialLinksValue extends Value
{
    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Attribute\Value\Value\SelectedSocialLink",
     *     cascade={"persist", "remove"}, mappedBy="value")
     * @ORM\JoinColumn(name="avID", referencedColumnName="avID")
     */
    protected $links;

    public function __construct()
    {
        parent::__construct();
        $this->links = new ArrayCollection();
    }

    public function getSelectedLinks()
    {
        return $this->links;
    }

    public function setSelectedLinks($links)
    {
        $this->links = $links;
    }
}
