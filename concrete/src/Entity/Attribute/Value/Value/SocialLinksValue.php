<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Concrete\Core\Sharing\SocialNetwork\Service;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atSocialLinks")
 */
class SocialLinksValue extends AbstractValue
{
    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Attribute\Value\Value\SelectedSocialLink",
     *     cascade={"persist", "remove"}, mappedBy="value")
     * @ORM\JoinColumn(name="avID", referencedColumnName="avID")
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

    public function __toString()
    {
        $html = '';
        $services = $this->getSelectedLinks();
        if (count($services) > 0) {
            foreach ($services as $link) {
                $html .= filter_var($link->getServiceInfo(), FILTER_VALIDATE_URL) . "\r\n";
            }
        }

        return $html;
    }
}
