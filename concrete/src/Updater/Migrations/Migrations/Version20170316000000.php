<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Entity\Attribute\Key\Settings\DateTimeSettings;
use Concrete\Core\Page\Page;
use SinglePage;
use Concrete\Core\Support\Facade\Application;

class Version20170316000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
	    $entities = \Core::make('express')->getEntities(true)->findAll();
	    foreach ($entities as $entity) {
	    	/* @var $entity \Concrete\Core\Entity\Express\Entity */
		    $category = $entity->getAttributeKeyCategory();
		    foreach($entity->getAttributes() as $key) {
			    $category->getSearchIndexer()->updateRepositoryColumns($category, $key);
		    }
	    }
    }

    public function down(Schema $schema)
    {
    }
}
