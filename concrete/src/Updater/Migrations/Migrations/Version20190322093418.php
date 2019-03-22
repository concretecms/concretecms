<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\ObjectInterface as AttributeObjectInterface;
use Concrete\Core\Entity\Attribute\Key\Key as AttributeKey;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\File\Version as FileVersion;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\User\UserInfo;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190322093418 extends AbstractMigration
{
    /**
     * @var string[] An array containing the object names for each attribute
     *               category to be used for updating the objects of each
     *               category. The keys of the array are the attribute category
     *               handles for each object.
     */
    private $attributeCategoryObjectMap = [
        // Custom implementation classes
        'collection' => Page::class,
        'user' => UserInfo::class,

        // Entity classes
        'file' => FileVersion::class,
        'site' => Site::class,
        'event' => CalendarEvent::class,
    ];

    /**
     * {@inheritdoc}
     *
     * @see AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $em = $this->connection->createEntityManager();

        $qb = $em->createQueryBuilder();
        $query = $qb->select('ak')
            ->from(AttributeKey::class, 'ak')
            ->innerJoin('ak.type', 'at')
            ->where('at.atHandle = :atHandle')
            ->setParameter('atHandle', 'number')
            ->getQuery()
        ;

        $checkForClear = $this->getObjectAttributesForClearingCheck();

        foreach ($checkForClear as $class => $keys) {
            $this->checkObjectForAttributeClearing($class, $keys);
        }
    }

    /**
     * Fetches a list of all attributes of type `number` in the system and their
     * related objects based on the mapped object class in the
     * `$attributeCategoryObjectMap` array.
     *
     * The returned array looks simlar to this:
     * [
     *     Page::class => ['page_number_attr1', 'page_number_attr1'],
     *     UserInfo::class => ['user_number_attr1', 'user_number_attr1'],
     *     FileVersion::class => ['file_number_attr1', 'file_number_attr1'],
     *     ...
     * ]
     *
     * @return string[] An array of all number attributes to be checked for
     *                  clearing for the target objects. The target object
     *                  classes are the keys of the array and the values are
     *                  arrays of the number attribute handles to be checked for
     *                  clearing.
     */
    private function getObjectAttributesForClearingCheck()
    {
        $em = $this->connection->createEntityManager();

        $qb = $em->createQueryBuilder();
        $query = $qb->select('ak')
            ->from(AttributeKey::class, 'ak')
            ->innerJoin('ak.type', 'at')
            ->where('at.atHandle = :atHandle')
            ->setParameter('atHandle', 'number')
            ->getQuery()
        ;

        $checkObjects = [];

        // Iterate over all number attributes and store which attributes need
        // to be inspected for each object.
        $iterableResult = $query->iterate();
        foreach ($iterableResult as $row) {
            $ak = $row[0];
            $categoryHandle = $ak->getAttributeCategoryEntity()
                ->getAttributeKeyCategoryHandle()
            ;

            if (array_key_exists($categoryHandle, $this->attributeCategoryObjectMap)) {
                $class = $this->attributeCategoryObjectMap[$categoryHandle];

                if (!isset($checkObjects[$class])) {
                    $checkObjects[$class] = [];
                }
                $checkObjects[$class][] = $ak->getAttributeKeyHandle();
            }

            // Execute all pending updates and detach the objects from Doctrine
            // to preserve memory.
            $em->flush();
            $em->clear();
        }

        return $checkObjects;
    }

    /**
     * Checks through the list of all entries of the given class whether their
     * number attributes should be cleared or not. If a value hasn't been
     * assigned for the number attribute, it will be cleared to set the database
     * column's value to the attribute type's default.
     *
     * @param  string $class the name of the class for which to check through
     *                       the list of entries
     * @param  string[] $attributeKeyHandles An array of all number attribute
     *                                       key handles that should be checked
     *                                       for clearing
     */
    private function checkObjectForAttributeClearing(
        $class,
        $attributeKeyHandles
    ) {
        $em = $this->connection->createEntityManager();

        // For the objects that do not implement the attribute methods on
        // their entity classes or do not have entity defined for them at
        // all, we need to do the update manually knowing their context.
        if ($class === Page::class) {
            $qb = $this->connection->createQueryBuilder();

            $stmt = $qb->select('p.cID')->from('Pages', 'p')->execute();
            while ($row = $stmt->fetch()) {
                $page = Page::getByID($row['cID']);

                $this->clearAttributeValues($page, $attributeKeyHandles);
            }
        } elseif ($class === UserInfo::class) {
            $qb = $this->connection->createQueryBuilder();

            $stmt = $qb->select('u.uID')->from('Users', 'u')->execute();
            while ($row = $stmt->fetch()) {
                $ui = UserInfo::getByID($row['uID']);

                $this->clearAttributeValues($ui, $attributeKeyHandles);
            }
        } else {
            // For doctrine entities we can do the update more easily
            // without knowing the context details of each object.
            $qb = $em->createQueryBuilder();
            $query = $qb->select('obj')->from($class, 'obj')->getQuery();

            $ir = $query->iterate();
            foreach ($ir as $subRow) {
                $this->clearAttributeValues($subRow[0], $attributeKeyHandles);
            }

            // Execute all pending updates and detach the objects from
            // Doctrine to preserve memory.
            $em->flush();
            $em->clear();
        }
    }

    /**
     * Clears the attribute values for the given object and each of the
     * attribute keys passed in the second parameter.
     *
     * @param AttributeObjectInterface $object the target object to clear the
     *                                         attributes for
     * @param string[] $attributeKeyHandles An array of attribute key handles
     *                                      that need to be cleared
     */
    private function clearAttributeValues(
        AttributeObjectInterface $object,
        $attributeKeyHandles
    ) {
        foreach ($attributeKeyHandles as $akHandle) {
            // Only clear the attribute if its current value is null. This does
            // not currently show up in the database search index table making
            // it impossible to only fetch such objects directly for the update.
            if ($object->getAttribute($akHandle) === null) {
                // Set some value for the attribute for it to be
                // clearable by the indexer.
                $object->setAttribute($akHandle, 0);

                // Clear the value because it is null for the object.
                $object->clearAttribute($akHandle);
            }
        }
    }
}
