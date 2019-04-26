<?php
namespace Concrete\Core\Entity\Site;

use Concrete\Core\Entity\LocaleTrait;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Localization\Locale\LocaleInterface;
use Concrete\Core\Multilingual\Service\UserInterface\Flag;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SiteSkeletonLocales")
 */
class SkeletonLocale implements LocaleInterface, LocaleEntityInterface, ExportableInterface
{

    use LocaleTrait;

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $skeletonLocaleID;

    /**
     * @ORM\ManyToOne(targetEntity="Skeleton", inversedBy="locales")
     * @ORM\JoinColumn(name="siteSkeletonID", referencedColumnName="siteSkeletonID")
     **/
    protected $skeleton;

    /**
     * @ORM\OneToOne(targetEntity="SkeletonTree", cascade={"all"}, mappedBy="locale")
     * @ORM\JoinColumn(name="siteTreeID", referencedColumnName="siteTreeID")
     **/
    protected $tree;

    public function getLocaleID()
    {
        return $this->skeletonLocaleID;
    }

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

    /**
     * @return mixed
     */
    public function getSiteTree()
    {
        return $this->tree;
    }

    /**
     * @param mixed $tree
     */
    public function setSiteTree($tree)
    {
        $this->tree = $tree;
    }

}
