<?php
namespace Concrete\Core\Entity\Page\Relation;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="MultilingualPageRelations"
 * )
 */
class MultilingualRelation extends Relation
{

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $mpLocale = '';

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    protected $mpLanguage = '';

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->mpLocale;
    }

    /**
     * @param mixed $mpLocale
     */
    public function setLocale($mpLocale)
    {
        $this->mpLocale = $mpLocale;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->mpLanguage;
    }

    /**
     * @param mixed $mpLanguage
     */
    public function setLanguage($mpLanguage)
    {
        $this->mpLanguage = $mpLanguage;
    }




}
