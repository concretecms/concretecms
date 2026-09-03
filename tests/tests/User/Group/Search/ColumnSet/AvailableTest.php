<?php

namespace Concrete\Tests\User\Group\Search\ColumnSet;

use Concrete\Core\Tree\Node\Type\Group as GroupTreeNode;
use Concrete\Core\Tree\Node\Type\GroupFolder;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\Search\ColumnSet\Available;
use Concrete\Tests\TestCase;
use Mockery as M;

/**
 * The group search grid renders each column value as raw HTML - the results template interpolates it
 * with Underscore's <%= %>, which does not escape - so these callbacks are the last place the value
 * can be made safe.
 *
 * @covers \Concrete\Core\User\Group\Search\ColumnSet\Available
 */
class AvailableTest extends TestCase
{

    const PAYLOAD = '<img src=x onerror=alert(1)>';

    /**
     * A folder name reached the grid unescaped.
     */
    public function testGroupFolderNameIsEscaped()
    {
        $node = M::mock(GroupFolder::class);
        $node->shouldReceive('getTreeNodeTypeHandle')->andReturn('group_folder');
        $node->shouldReceive('getTreeNodeName')->andReturn(self::PAYLOAD);

        $this->assertSame(h(self::PAYLOAD), Available::getGroupName($node));
    }

    /**
     * The other half of the same method: a group name reaches the same column through the same
     * template, and was still being handed over as markup.
     */
    public function testGroupNameIsEscaped()
    {
        $group = M::mock(Group::class);
        $group->shouldReceive('getGroupName')->andReturn(self::PAYLOAD);

        $node = M::mock(GroupTreeNode::class);
        $node->shouldReceive('getTreeNodeTypeHandle')->andReturn('group');
        $node->shouldReceive('getTreeNodeGroupObject')->andReturn($group);

        $value = Available::getGroupName($node);

        $this->assertStringNotContainsString('<img', $value, 'A group name must not reach the grid as markup.');
        $this->assertSame(h(self::PAYLOAD), $value);
    }

    /**
     * The member count shares the grid's raw interpolation, so it must stay numeric.
     */
    public function testMemberCountIsAnInteger()
    {
        $group = M::mock(Group::class);
        $group->shouldReceive('getGroupMembersNum')->andReturn('7' . self::PAYLOAD);

        $node = M::mock(GroupTreeNode::class);
        $node->shouldReceive('getTreeNodeTypeHandle')->andReturn('group');
        $node->shouldReceive('getTreeNodeGroupObject')->andReturn($group);

        $this->assertSame(7, Available::getMemberCount($node));
    }

    public function testFolderRowsHaveNoMemberCount()
    {
        $node = M::mock(GroupFolder::class);
        $node->shouldReceive('getTreeNodeTypeHandle')->andReturn('group_folder');

        $this->assertSame('', Available::getMemberCount($node));
    }
}
