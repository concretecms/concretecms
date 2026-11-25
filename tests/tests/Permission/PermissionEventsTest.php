<?php

namespace Concrete\Tests\Permission;

use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Access\Entity\Type as AccessEntityType;
use Concrete\Core\Permission\Category as PermissionCategory;
use Concrete\Core\Permission\Event\PermissionAssignmentEvent;
use Concrete\Core\Permission\Event\PermissionInheritanceEvent;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Permission\Key\PageKey;
use Concrete\TestHelpers\Page\PageTestCase;
use Group;

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

        $assignFired = false;
        $clearFired = false;
        $director = $this->app->make('director');
        $assignListener = function ($event) use (&$assignFired, $pa) {
            $this->assertInstanceOf(PermissionAssignmentEvent::class, $event);
            if ($event->getPermissionKey() && $event->getPermissionKey()->getPermissionKeyHandle() === 'view_page') {
                $this->assertSame($pa, $event->getPermissionAccess());
                $assignFired = true;
            }
        };
        $clearListener = function ($event) use (&$clearFired) {
            $this->assertInstanceOf(PermissionAssignmentEvent::class, $event);
            if ($event->getPermissionKey() && $event->getPermissionKey()->getPermissionKeyHandle() === 'view_page') {
                $clearFired = true;
            }
        };
        $director->addListener('on_permission_assignment_assign', $assignListener);
        $director->addListener('on_permission_assignment_clear', $clearListener);

        try {
            $pa->addListItem($entity);
            $pt = $pk->getPermissionAssignmentObject();
            $pt->assignPermissionAccess($pa);
            $this->assertTrue($assignFired, 'on_permission_assignment_assign should fire when assigning permission access');

            $pt->clearPermissionAssignment();
            $this->assertTrue($clearFired, 'on_permission_assignment_clear should fire when clearing assignment');
        } finally {
            $director->removeListener('on_permission_assignment_assign', $assignListener);
            $director->removeListener('on_permission_assignment_clear', $clearListener);
            $page->delete();
        }
    }

    public function testPermissionInheritanceEvent()
    {
        $page = self::createPage('Permission Inheritance Test Page');

        $inheritanceFired = false;
        $firedMode = null;
        $director = $this->app->make('director');
        $listener = function ($event) use (&$inheritanceFired, &$firedMode, $page) {
            $this->assertInstanceOf(PermissionInheritanceEvent::class, $event);
            $this->assertEquals($page->getCollectionID(), $event->getPermissionObject()->getCollectionID());
            $inheritanceFired = true;
            $firedMode = $event->getInheritanceMode();
        };
        $director->addListener('on_permission_inheritance_change', $listener);

        try {
            $page->setPermissionsToManualOverride();
            $this->assertTrue($inheritanceFired, 'on_permission_inheritance_change should fire when overriding permissions');
            $this->assertEquals('OVERRIDE', $firedMode);

            $inheritanceFired = false;
            $page->inheritPermissionsFromParent();
            $this->assertTrue($inheritanceFired, 'on_permission_inheritance_change should fire when inheriting from parent');
            $this->assertEquals('PARENT', $firedMode);
        } finally {
            $director->removeListener('on_permission_inheritance_change', $listener);
            $page->delete();
        }
    }
}
