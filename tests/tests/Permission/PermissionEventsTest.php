<?php

namespace Concrete\Tests\Permission;

use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Access\Entity\Type as AccessEntityType;
use Concrete\Core\Permission\Category as PermissionCategory;
use Concrete\Core\Permission\Event\PermissionAccessEntityEvent;
use Concrete\Core\Permission\Event\PermissionAssignmentEvent;
use Concrete\Core\Permission\Event\PermissionInheritanceEvent;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Permission\Key\PageKey;
use Concrete\TestHelpers\Page\PageTestCase;
use Events;
use Group;
use Page;

class PermissionEventsTest extends PageTestCase
{
    protected function getTables()
    {
        return array_merge(parent::getTables(), [
            'UserGroups',
            'Groups',
            'TreeTypes',
            'Trees',
            'TreeNodes',
            'TreeNodeTypes',
            'TreeGroupNodes',
            'AreaPermissionAssignments',
            'PermissionAccess',
            'PermissionAccessEntities',
            'PermissionAccessEntityGroups',
            'PermissionAccessList',
            'PermissionKeyCategories',
            'PermissionKeys',
        ]);
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (!AccessEntityType::getByHandle('group')) {
            AccessEntityType::add('group', 'Group');
        }
        if (!PermissionCategory::getByHandle('page')) {
            PermissionCategory::add('page');
        }
        if (!PermissionKey::getByHandle('view_page')) {
            PermissionKey::add('page', 'view_page', 'View Page', '', 0, 0);
        }
    }

    public function testPermissionAssignmentEvents()
    {
        $page = self::createPage('Permission Test Page');
        $pk = PageKey::getByHandle('view_page');
        $this->assertNotNull($pk, 'Permission key view_page should exist');
        $pk->setPermissionObject($page);

        $pa = Access::create($pk);
        $group = Group::getByID(GUEST_GROUP_ID);
        if (!is_object($group)) {
            $group = Group::add('Guest', '');
        }
        $entity = GroupEntity::getOrCreate($group);

        $addEntityFired = false;
        $removeEntityFired = false;
        $assignFired = false;
        $clearFired = false;

        Events::addListener('on_permission_access_entity_add', function ($event) use (&$addEntityFired, $entity) {
            $this->assertInstanceOf(PermissionAccessEntityEvent::class, $event);
            $this->assertEquals($entity->getAccessEntityID(), $event->getAccessEntity()->getAccessEntityID());
            $addEntityFired = true;
        });

        Events::addListener('on_permission_access_entity_remove', function ($event) use (&$removeEntityFired, $entity) {
            $this->assertInstanceOf(PermissionAccessEntityEvent::class, $event);
            $this->assertEquals($entity->getAccessEntityID(), $event->getAccessEntity()->getAccessEntityID());
            $removeEntityFired = true;
        });

        Events::addListener('on_permission_assignment_assign', function ($event) use (&$assignFired, $pk) {
            $this->assertInstanceOf(PermissionAssignmentEvent::class, $event);
            if ($event->getPermissionKey() && $event->getPermissionKey()->getPermissionKeyHandle() === 'view_page') {
                $assignFired = true;
            }
        });

        Events::addListener('on_permission_assignment_clear', function ($event) use (&$clearFired, $pk) {
            $this->assertInstanceOf(PermissionAssignmentEvent::class, $event);
            if ($event->getPermissionKey() && $event->getPermissionKey()->getPermissionKeyHandle() === 'view_page') {
                $clearFired = true;
            }
        });

        $pa->addListItem($entity);
        $this->assertTrue($addEntityFired, 'on_permission_access_entity_add should fire when adding list item');

        $pt = $pk->getPermissionAssignmentObject();
        $pt->assignPermissionAccess($pa);
        $this->assertTrue($assignFired, 'on_permission_assignment_assign should fire when assigning permission access');

        $pt->clearPermissionAssignment();
        $this->assertTrue($clearFired, 'on_permission_assignment_clear should fire when clearing assignment');

        $pa->removeListItem($entity);
        $this->assertTrue($removeEntityFired, 'on_permission_access_entity_remove should fire when removing list item');

        $page->delete();
    }

    public function testPermissionInheritanceEvent()
    {
        $page = self::createPage('Permission Inheritance Test Page');

        $inheritanceFired = false;
        $firedMode = null;

        Events::addListener('on_permission_inheritance_change', function ($event) use (&$inheritanceFired, &$firedMode, $page) {
            $this->assertInstanceOf(PermissionInheritanceEvent::class, $event);
            $this->assertEquals($page->getCollectionID(), $event->getPermissionObject()->getCollectionID());
            $inheritanceFired = true;
            $firedMode = $event->getInheritanceMode();
        });

        $page->setPermissionsToManualOverride();
        $this->assertTrue($inheritanceFired, 'on_permission_inheritance_change should fire when overriding permissions');
        $this->assertEquals('OVERRIDE', $firedMode);

        $inheritanceFired = false;
        $page->inheritPermissionsFromParent();
        $this->assertTrue($inheritanceFired, 'on_permission_inheritance_change should fire when inheriting from parent');
        $this->assertEquals('PARENT', $firedMode);

        $page->delete();
    }
}
