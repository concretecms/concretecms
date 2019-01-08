<?php

namespace Concrete\Tests\Express;

use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Database;
use Express;

class AssociationApplierTest extends ConcreteDatabaseTestCase
{
    protected $tables = [
        'Trees',
        'TreeNodes',
        'TreeGroupNodes',
        'TreeTypes',
        'TreeNodeTypes',
        'TreeNodePermissionAssignments',
        'PermissionAccessEntities',
        'PermissionAccessEntityGroups',
        'PermissionAccessEntityTypes',
        'PermissionKeys',
        'PermissionKeyCategories',
        'Groups',
    ];

    protected $metadatas = [
        'Concrete\Core\Entity\Express\Entity',
        'Concrete\Core\Entity\Express\Entry',
        'Concrete\Core\Entity\Express\Entry\Association',
        'Concrete\Core\Entity\Express\Entry\AssociationEntry',
        'Concrete\Core\Entity\Attribute\Value\ExpressValue',
        'Concrete\Core\Entity\Attribute\Value\Value\Value',
        'Concrete\Core\Entity\Attribute\Value\Value\TextValue',
        'Concrete\Core\Entity\Attribute\Value\Value\ExpressValue',
        'Concrete\Core\Entity\Attribute\Value\Value\AddressValue',
        'Concrete\Core\Entity\Express\Form',
        'Concrete\Core\Entity\Express\FieldSet',
        'Concrete\Core\Entity\Express\Control\Control',
        'Concrete\Core\Entity\Express\Control\AttributeKeyControl',
        'Concrete\Core\Entity\Express\Control\AssociationControl',
        'Concrete\Core\Entity\Express\Association',
        'Concrete\Core\Entity\Attribute\Type',
        'Concrete\Core\Entity\Attribute\Key\ExpressKey',
        'Concrete\Core\Entity\Attribute\Key\Key',
        'Concrete\Core\Entity\Attribute\Key\Settings\TextSettings',
        'Concrete\Core\Entity\Attribute\Key\Settings\AddressSettings',
        'Concrete\Core\Entity\Attribute\Key\Settings\TextareaSettings',
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->truncateTables();

        \Concrete\Core\Tree\Node\NodeType::add('category');
        \Concrete\Core\Tree\Node\NodeType::add('express_entry_category');
        \Concrete\Core\Tree\TreeType::add('express_entry_results');
        \Concrete\Core\Tree\Node\NodeType::add('express_entry_results');

        $tree = \Concrete\Core\Tree\Type\ExpressEntryResults::add();

        $factory = \Core::make('\Concrete\Core\Attribute\TypeFactory');
        $factory->add('text', 'Text');
        $factory->add('address', 'Address');
        $factory->add('textarea', 'Textarea');
    }

    public function testOneToManyAll()
    {
        $this->createProjectData();

        // Websites
        $this->addOneToManyAssociationAndTestIt(1, [6, 8, 11], [
            1 => [6, 8, 11],
        ]);

        // Mobile
        $this->addOneToManyAssociationAndTestIt(3, [12], [
            1 => [6, 8, 11], 3 => [12],
        ]);

        // Let's change one
        $this->addOneToManyAssociationAndTestIt(1, [6, 11], [
            1 => [6, 11], 3 => [12],
        ]);

        $this->assertNoCategory(8);

        $this->removeOneToManyAssociationAndTestIt(1);

        $this->addOneToManyAssociationAndTestIt(5, [9, 10], [
            3 => [12], 5 => [9, 10],
        ]);

        $this->assertNoCategory(8);

        // Test the final amount of rows in the table
        $db = \Database::connection();
        $cnt = $db->getOne('select count(*) from ExpressEntityAssociationEntries');
        $this->assertEquals(6, $cnt);
    }

    public function testOneToManyCategoryUpdate()
    {
        // This test is meant to test the following:
        // We have a one to many category -> project setup. We save two projects against a category
        // We save another category against one of those projects. At most each project should only
        // have ONE category.

        $this->createProjectData();

        $this->addOneToManyAssociationAndTestIt(1, [6, 8, 11], [
            1 => [6, 8, 11],
        ]);

        $this->addOneToManyAssociationAndTestIt(2, [6], [
            1 => [8, 11], 2 => [6],
        ]);
    }

    public function testManyToOneSaveHandler()
    {
        $this->createProjectData();

        $this->addManyToOneAssociationAndTestIt(6, 1, [6]);
        $this->addManyToOneAssociationAndTestIt(8, 1, [6, 8]);
        $this->addManyToOneAssociationAndTestIt(11, 1, [6, 8, 11]);
        $this->addManyToOneAssociationAndTestIt(12, 3, [12]);
        $this->addManyToOneAssociationAndTestIt(9, 5, [9]);
        $this->addManyToOneAssociationAndTestIt(10, 5, [9, 10]);

        $this->removeManyToOneAssociationAndTestIt(9);
        $this->removeManyToOneAssociationAndTestIt(10);

        $this->assertNoCategory(9);
        $this->assertNoProjects(5);

        $this->addManyToOneAssociationAndTestIt(9, 5, [9]);

        $this->removeManyToOneAssociationAndTestIt(6);
        $this->removeManyToOneAssociationAndTestIt(8);

        $this->addManyToOneAssociationAndTestIt(9, 1, [11, 9]);
    }

    public function testMixedManyToOneSaveHandler()
    {
        $this->createProjectData();

        $this->addOneToManyAssociationAndTestIt(1, [6, 8, 11], [
            1 => [6, 8, 11],
        ]);

        $this->addManyToOneAssociationAndTestIt(8, 3, [8]);

        $this->addOneToManyAssociationAndTestIt(5, [9, 10], [
            1 => [6, 11], 3 => [8], 5 => [9, 10],
        ]);

        $this->addManyToOneAssociationAndTestIt(7, 3, [8, 7]);
    }

    public function testManyToManySaveHandler()
    {
        $this->createProjectData('ManyToMany');
        $this->addManyToManyAssociationAndTestIt(1, [6, 8, 11], [
            1 => [6, 8, 11],
        ], [
            6 => [1],
            8 => [1],
            11 => [1],
        ]);
        $this->addManyToManyAssociationAndTestIt(7, [1, 3], [
            1 => [6, 8, 11, 7], 3 => [7],
        ], [
            6 => [1],
            7 => [1, 3],
            8 => [1],
            11 => [1],
        ]);
        $this->addManyToManyAssociationAndTestIt(7, [1, 3, 4], [
            1 => [6, 8, 11, 7], 3 => [7], 4 => [7],
        ], [
            6 => [1],
            7 => [1, 3, 4],
            8 => [1],
            11 => [1],
        ]);

        $this->addManyToManyAssociationAndTestIt(1, [6], [
            1 => [6], 3 => [7], 4 => [7],
        ], [
            6 => [1],
            7 => [3, 4],
        ]);

        $this->assertNoCategories(8);
        $this->assertNoCategories(11);
    }

    public function testOneToManyDuplicates()
    {
        $this->createProjectData();
        $db = \Database::connection();

        // Websites
        $this->addOneToManyAssociationAndTestIt(1, [6, 8, 11], [
            1 => [6, 8, 11],
        ]);

        $this->assertEquals(3, $db->fetchColumn('select count(*) from ExpressEntityAssociationEntries where association_id = 1'));
        $this->assertEquals(1, $db->fetchColumn('select count(*) from ExpressEntityAssociationEntries where association_id = 2'));
        $this->assertEquals(1, $db->fetchColumn('select count(*) from ExpressEntityAssociationEntries where association_id = 3'));
        $this->assertEquals(1, $db->fetchColumn('select count(*) from ExpressEntityAssociationEntries where association_id = 4'));

        $this->addOneToManyAssociationAndTestIt(1, [6, 8, 11], [
            1 => [6, 8, 11],
        ]);

        $this->assertEquals(3, $db->fetchColumn('select count(*) from ExpressEntityAssociationEntries where association_id = 1'));
        $this->assertEquals(1, $db->fetchColumn('select count(*) from ExpressEntityAssociationEntries where association_id = 5'));
        $this->assertEquals(1, $db->fetchColumn('select count(*) from ExpressEntityAssociationEntries where association_id = 6'));
        $this->assertEquals(1, $db->fetchColumn('select count(*) from ExpressEntityAssociationEntries where association_id = 7'));

    }

    public function testManyToManyDuplicates()
    {
        $this->createProjectData('ManyToMany');

        $this->addManyToManyAssociationAndTestIt(1, [6, 8, 11], [
            1 => [6, 8, 11],
        ], [
            6 => [1],
            8 => [1],
            11 => [1],
        ]);

        $db = \Database::connection();
        $this->assertEquals(3, $db->fetchColumn('select count(*) from ExpressEntityAssociationEntries where association_id = 1'));
        $this->assertEquals(1, $db->fetchColumn('select count(*) from ExpressEntityAssociationEntries where association_id = 2'));
        $this->assertEquals(1, $db->fetchColumn('select count(*) from ExpressEntityAssociationEntries where association_id = 3'));
        $this->assertEquals(1, $db->fetchColumn('select count(*) from ExpressEntityAssociationEntries where association_id = 4'));


        $this->addManyToManyAssociationAndTestIt(1, [6, 8, 11], [
            1 => [6, 8, 11],
        ], [
            6 => [1],
            8 => [1],
            11 => [1],
        ]);

        $this->assertEquals(3, $db->fetchColumn('select count(*) from ExpressEntityAssociationEntries where association_id = 1'));
        $this->assertEquals(1, $db->fetchColumn('select count(*) from ExpressEntityAssociationEntries where association_id = 2'));
        $this->assertEquals(1, $db->fetchColumn('select count(*) from ExpressEntityAssociationEntries where association_id = 3'));
        $this->assertEquals(1, $db->fetchColumn('select count(*) from ExpressEntityAssociationEntries where association_id = 4'));

    }

    public function testOneToOneSaveHandler()
    {
        $this->createProjectData('OneToOne');
        $this->addOneToOneAssociationAndTestIt(3, 11);
        $this->addOneToOneAssociationAndTestIt(8, 1);
        $this->addOneToOneAssociationAndTestIt(6, 1);
        $this->addOneToOneAssociationAndTestIt(3, 12);
        $this->addOneToOneAssociationAndTestIt(1, 8);
    }

    protected function createProjectData($association = 'OneToMany')
    {
        $categoryBuilder = Express::buildObject('category', 'categories', 'Category');
        $categoryBuilder->addAttribute('text', 'Category Name', 'category_name');
        $category = $categoryBuilder->save();

        $projectBuilder = Express::buildObject('project', 'projects', 'Project');
        $projectBuilder->addAttribute('text', 'Project Name', 'project_name');
        $project = $projectBuilder->save();

        $builder = $categoryBuilder->buildAssociation();
        switch ($association) {
            case 'OneToOne':
                $builder->addOneToOne($projectBuilder);
                break;
            case 'ManyToMany':
                $builder->addManyToMany($projectBuilder);
                break;
            default:
                $builder->addOneToMany($projectBuilder);
                break;
        }
        $builder->save();

        foreach ([
                    'Web', 'Print', 'Mobile', 'Billboard', 'Broadcast',
                ] as $name) {
            $builder = Express::buildEntry($category)
                ->setCategoryName($name);
            $builder->save();
        }

        foreach ([
                    'Website A', 'Banner Ad', 'Website B', 'Narration', 'Voiceover', 'Website C', 'Game',
                ] as $name) {
            $builder = Express::buildEntry($project)
                ->setProjectName($name);
            $builder->save();
        }
    }

    protected function addManyToOneAssociationAndTestIt($projectID, $categoryID, $results, $debug = false)
    {
        $db = \Database::connection();
        $em = $db->getEntityManager();

        $project = Express::getEntry($projectID);
        $category = Express::getEntry($categoryID);

        $projectEntity = Express::getObjectByHandle('project');
        $association = $projectEntity->getAssociation('category');
        $applier = new \Concrete\Core\Express\Association\Applier($em);
        $applier->associateManyToOne($association, $project, $category);

        Express::refresh($project);

        if ($debug) {
            $this->debugAndExit();
        }

        $category = $project->getCategory();
        $this->assertEquals($categoryID, $category->getID());

        Express::refresh($category);
        $projects = $category->getProjects();
        $i = 0;
        foreach ($projects as $project) {
            // we have to do a foreach because sometimes the keys get screwed up by doctrine
            $projectID = $results[$i];
            $this->assertEquals($projectID, $project->getID());
            ++$i;
        }

        // Also, verify that the amount of entries in the associations table for the inverse matches
        // the count we passed in of the $results variable.

        $count = $db->getOne('select count(*) from ExpressEntityEntryAssociations a inner join ExpressEntityAssociationEntries ae on a.id = ae.association_id where a.exEntryID = ?', [$category->getID()]);
        $this->assertEquals(count($results), $count);
    }

    protected function addOneToOneAssociationAndTestIt($entryID, $associatedEntryID, $debug = false)
    {
        $db = \Database::connection();
        $em = $db->getEntityManager();

        $entry = Express::getEntry($entryID);
        $associatedEntry = Express::getEntry($associatedEntryID);

        if ($entry->getEntity()->getHandle() == 'category') {
            $categoryEntity = Express::getObjectByHandle('category');
            $association = $categoryEntity->getAssociation('project');
        } else {
            $projectEntity = Express::getObjectByHandle('project');
            $association = $projectEntity->getAssociation('category');
        }

        $applier = new \Concrete\Core\Express\Association\Applier($em);
        $applier->associateOneToOne($association, $entry, $associatedEntry);

        Express::refresh($entry);

        if ($entry->getEntity()->getHandle() == 'category') {
            $project = $entry->getProject();
            Express::refresh($project);
            if ($debug) {
                $this->debugAndExit();
            }
            $this->assertEquals($associatedEntryID, $project->getID());
            $category = $project->getCategory();
            $this->assertEquals($entryID, $category->getID());
        } else {
            $category = $entry->getCategory();
            Express::refresh($category);
            if ($debug) {
                $this->debugAndExit();
            }
            $this->assertEquals($associatedEntryID, $category->getID());
            $project = $category->getAssociations()[0]->getSelectedEntry();
            $project = $category->getProject();
            $this->assertEquals($entryID, $project->getID());
        }

        // Also, verify that the amount of entries in the associations table for the inverse matches
        // the count we passed in of the $results variable.

        $count = $db->getOne('select count(*) from ExpressEntityEntryAssociations a inner join ExpressEntityAssociationEntries ae on a.id = ae.association_id where a.exEntryID = ?', [$category->getID()]);
        $this->assertEquals(1, $count);

        $count = $db->getOne('select count(*) from ExpressEntityEntryAssociations a inner join ExpressEntityAssociationEntries ae on a.id = ae.association_id where a.exEntryID = ?', [$project->getID()]);
        $this->assertEquals(1, $count);
    }

    protected function addOneToManyAssociationAndTestIt($entryID, $associationEntryIDs, $results, $debug = false)
    {
        $db = \Database::connection();
        $em = $db->getEntityManager();

        // Empty One-To-Many
        $entry = Express::getEntry($entryID);

        $entries = [];
        foreach ($associationEntryIDs as $associationEntryID) {
            $entries[] = Express::getEntry($associationEntryID);
        }

        $category = Express::getObjectByHandle('category');
        $association = $category->getAssociation('projects');
        $applier = new \Concrete\Core\Express\Association\Applier($em);
        $applier->associateOneToMany($association, $entry, $entries);

        // Now lets see if the results are right.

        if ($debug) {
            $this->debugAndExit();
        }

        foreach ($results as $entryID => $associationEntryIDs) {
            $entry = Express::getEntry($entryID);
            Express::refresh($entry);
            $projects = $entry->getProjects();
            $this->assertEquals(count($associationEntryIDs), count($projects));
            $i = 0;
            foreach ($projects as $project) {
                $associationEntryID = $associationEntryIDs[$i];
                $this->assertEquals($associationEntryID, $project->getId());
                ++$i;
            }

            foreach ($projects as $project) {
                Express::refresh($project);
                $category = $project->getCategory();
                $this->assertEquals($entry->getId(), $category->getID());

                // Also, verify that there is only one database row in the entries table matching the entry ID to
                // the category
                $count = $db->getOne('select count(*) from ExpressEntityEntryAssociations a inner join ExpressEntityAssociationEntries ae on a.id = ae.association_id where a.exEntryID = ?', [$project->getID()]);
                $this->assertEquals(1, $count);
            }
        }
    }

    protected function debugAndExit()
    {
        $db = Database::connection();

        fwrite(STDERR, "Current status:\n");

        $category = Express::getObjectByHandle('category');
        $list = new \Concrete\Core\Express\EntryList($category);
        $list->ignorePermissions();
        foreach ($list->getResults() as $category) {
            Express::refresh($category);
            fwrite(STDERR, "Category: {$category->getCategoryName()} ({$category->getId()})\n");
            $projectAssociation = $category->getAssociations()[0];
            if ($projectAssociation) {
                $entries = $db->GetCol('select exEntryID from ExpressEntityAssociationEntries where association_id = ? order by displayOrder asc ', [$projectAssociation->getId()]);
                foreach ($entries as $entry) {
                    fwrite(STDERR, "Found Related Project ID: {$entry}\n");
                }
            }
        }


        fwrite(STDERR, "-----------------------------------\n");


        $project = Express::getObjectByHandle('project');
        $list = new \Concrete\Core\Express\EntryList($project);
        $list->ignorePermissions();
        foreach ($list->getResults() as $project) {
            Express::refresh($project);
            fwrite(STDERR, "Project: {$project->getProjectName()} ({$project->getId()})\n");
            $categoryAssociation = $project->getAssociations()[0];
            if ($categoryAssociation) {
                $entries = $db->GetCol('select exEntryID from ExpressEntityAssociationEntries where association_id = ? order by displayOrder asc ', [$categoryAssociation->getId()]);
                foreach ($entries as $entry) {
                    fwrite(STDERR, "Found Related Category ID: {$entry}\n");
                }
            }
        }


        exit;
    }

    protected function addManyToManyAssociationAndTestIt($entryID, $associationEntryIDs, $entryResults, $inverseResults, $debug = false)
    {
        $db = \Database::connection();
        $em = $db->getEntityManager();

        // Empty One-To-Many
        $entry = Express::getEntry($entryID);

        $entries = [];
        foreach ($associationEntryIDs as $associationEntryID) {
            $entries[] = Express::getEntry($associationEntryID);
        }

        if ($entry->getEntity()->getHandle() == 'category') {
            $category = Express::getObjectByHandle('category');
            $association = $category->getAssociation('projects');
        } else {
            $project = Express::getObjectByHandle('project');
            $association = $project->getAssociation('categories');
        }
        $applier = new \Concrete\Core\Express\Association\Applier($em);
        $applier->associateManyToMany($association, $entry, $entries);

        if ($debug) {
            $this->debugAndExit();
        }

        // Now lets see if the results are right.
        foreach ($entryResults as $entryID => $associationEntryIDs) {
            $entry = Express::getEntry($entryID);
            Express::refresh($entry);
            $projects = $entry->getProjects();
            $this->assertEquals(count($associationEntryIDs), count($projects));
            $i = 0;
            foreach ($projects as $project) {
                $associationEntryID = $associationEntryIDs[$i];
                $this->assertEquals($associationEntryID, $project->getId());
                ++$i;
            }
        }

        foreach ($inverseResults as $entryID => $associationEntryIDs) {
            $entry = Express::getEntry($entryID);
            Express::refresh($entry);
            $categories = $entry->getCategories();
            $this->assertEquals(count($associationEntryIDs), count($categories));
            $i = 0;
            foreach ($categories as $category) {
                $associationEntryID = $associationEntryIDs[$i];
                $this->assertEquals($associationEntryID, $category->getId());
                ++$i;
            }
        }
    }

    protected function assertNoCategory($projectID)
    {
        $project = Express::getEntry($projectID);
        Express::refresh($project);
        $category = $project->getCategory();
        $this->assertTrue($category === null);
    }

    protected function assertNoProjects($categoryID)
    {
        $category = Express::getEntry($categoryID);
        Express::refresh($category);
        $projects = $category->getProjects();
        $this->assertCount(0, $projects);
    }

    protected function assertNoCategories($projectID)
    {
        $project = Express::getEntry($projectID);
        Express::refresh($project);
        $categories = $project->getCategories();
        $this->assertTrue(is_array($categories));
        $this->assertTrue(count($categories) == 0);
    }

    protected function removeManyToOneAssociationAndTestIt($projectID, $debug = false)
    {
        $db = \Database::connection();
        $em = $db->getEntityManager();

        $project = Express::getEntry($projectID);

        $categoryEntity = Express::getObjectByHandle('project');
        $association = $categoryEntity->getAssociation('category');
        $category = $project->getCategory();

        $applier = new \Concrete\Core\Express\Association\Applier($em);
        $applier->removeAssociation($association, $project);

        Express::refresh($project);
        Express::refresh($category);

        if ($debug) {
            $this->debugAndExit();
        }

        $projects = $category->getProjects();
        foreach ($projects as $project) {
            $this->assertTrue($project->getId() != $projectID);
        }
    }

    protected function removeOneToManyAssociationAndTestIt($categoryID, $debug = false)
    {
        $db = \Database::connection();
        $em = $db->getEntityManager();

        $category = Express::getEntry($categoryID);
        $projects = $category->getProjects();

        $categoryEntity = Express::getObjectByHandle('category');
        $association = $categoryEntity->getAssociation('projects');

        $applier = new \Concrete\Core\Express\Association\Applier($em);
        $applier->removeAssociation($association, $category);

        Express::refresh($category);

        if ($debug) {
            $this->debugAndExit();
        }

        foreach ($projects as $project) {
            $category = $project->getCategory();
            $this->assertNull($category);
        }
    }
}
