<?php
namespace Concrete\Core\Entity\Search;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SavedGroupSearchQueries")
 */
class SavedGroupSearch extends SavedSearch
{

    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;



}
