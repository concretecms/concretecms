<?php
namespace Concrete\Core\Board\DataSource\Saver;

use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
use Concrete\Core\Entity\Board\DataSource\Configuration\PageConfiguration;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Page\Search\Field\Manager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

defined('C5_EXECUTE') or die("Access Denied.");

class PageSaver extends AbstractSaver
{

    /**
     * @var Manager 
     */
    protected $searchFieldManager;

    public function __construct(Manager $searchFieldManager, EntityManager $entityManager)
    {
        $this->searchFieldManager = $searchFieldManager;
        parent::__construct($entityManager);
    }

    public function createConfiguration(Request $request)
    {
        $fields = $this->searchFieldManager->getFieldsFromRequest($request->request->all());
        $query = new Query();
        $query->setFields($fields);
        $query->setItemsPerPage(0); // has to be here but not used.
        $pageConfiguration = new PageConfiguration();
        $pageConfiguration->setQuery($query);
        return $pageConfiguration;
    }

    public function createConfigurationFromImport(\SimpleXMLElement $element): Configuration
    {
        $manager = app('manager/search_field/page');
        $fields = [];
        if (!empty($element->fields)) {
            foreach ($element->fields->field as $fieldNode) {
                $field = $manager->getFieldByKey((string) $fieldNode['key']);
                $field->loadDataFromImport($fieldNode);
                $fields[] = $field;
            }
        }
        $query = new Query();
        $query->setFields($fields);
        $query->setItemsPerPage(0); // has to be here but not used.
        $pageConfiguration = new PageConfiguration();
        $pageConfiguration->setQuery($query);
        return $pageConfiguration;
    }



}
