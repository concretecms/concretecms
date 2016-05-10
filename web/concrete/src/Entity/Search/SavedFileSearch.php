<?php
namespace Concrete\Core\Entity\Search;

/**
 * @Entity
 * @Table(name="SavedFileSearchQueries")
 */
class SavedFileSearch extends SavedSearch
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;



}
