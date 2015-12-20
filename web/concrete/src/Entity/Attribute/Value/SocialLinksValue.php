<?php
namespace Concrete\Core\Entity\Attribute\Value;

use Doctrine\Common\Collections\ArrayCollection;


/**
 * @Entity
 * @Table(name="SocialLinkAttributeValues")
 */
class SocialLinksValue extends Value
{

    /**
     * @OneToMany(targetEntity="\Concrete\Core\Entity\Attribute\Value\SelectedSocialLink", mappedBy="value")
     * @JoinColumn(name="avID", referencedColumnName="avID")
     */
    protected $links;

    public function __construct()
    {
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
