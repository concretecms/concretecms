<?php
namespace Concrete\Core\Form\Service\Widget;

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Tree\Node\Type\Group as GroupTreeNode;
use Concrete\Core\Tree\Type\Group as GroupTree;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupList;
use Concrete\Core\Utility\Service\Identifier;
use Symfony\Component\HttpFoundation\Request;

class GroupSelector
{

    protected $assetGroup;

    protected $request;

    protected $formHelper;

    protected $groupList;

    public function __construct(Form $formHelper, GroupList $groupList)
    {
        $this->assetGroup = ResponseAssetGroup::get();
        $this->request = Request::createFromGlobals();
        $this->formHelper = $formHelper;
        $this->groupList = $groupList;
    }

    public function selectGroup($field, $group = null, $noneText = null)
    {
        $selected = 0;
        if ($group) {
            $selected = ($group instanceof Group) ? $group->getGroupID() : $group;
        }
        if (!$noneText) {
            $noneText = t('** Select Group');
        }
        $groups = ['' => $noneText];
        foreach($this->groupList->getResults() as $groupResult) {
            $groups[$groupResult->getGroupID()] = $groupResult->getGroupDisplayName();
        }
        print $this->formHelper->select($field, $groups, $selected);
    }

    public function getGroupFromGroupTreeRequestValue($value)
    {
        // We have this helper method because you're not actually submitting a group ID – you're submitting a
        // group node ID. So we need to translate that into a group.
        if ($value) {
            $node = \Concrete\Core\Tree\Node\Type\Group::getByID($value);
            if ($node) {
                return $node->getTreeNodeGroupObject();
            }
        }
    }

    public function selectGroupWithTree($field, $group = null)
    {
        $identifier = new Identifier();
        $identifier = $identifier->getString(32);

        $selectedGroupID = 0;
        $selectedNodeID = 0;

        if ($this->request->getRealMethod() == 'POST') {
            if ($this->request->request->has($field)) {
                $selectedGroupID = intval($this->request->request->get($field));
            }
        } elseif ($group) {
            $selectedGroupID = ($group instanceof Group) ? $group->getGroupID() : $group;
        }

        // At this point selected is the ID of the group, but we need the group Node ID.
        if ($selectedGroupID > 0) {
            $selectedNode = \Concrete\Core\Tree\Node\Type\Group::getTreeNodeByGroupID($selectedGroupID);
            if ($selectedNode) {
                $selectedNodeID = $selectedNode->getTreeNodeID();
            }
        }

        $tree = GroupTree::get();
        $guestGroupNode = GroupTreeNode::getTreeNodeByGroupID(GUEST_GROUP_ID);
        $registeredGroupNode = GroupTreeNode::getTreeNodeByGroupID(REGISTERED_GROUP_ID);

        $guestGroupNodeID = $guestGroupNode->getTreeNodeID();
        $registeredGroupNodeID = $registeredGroupNode->getTreeNodeID();
        $treeID = $tree->getTreeID();

        $html = <<<EOL
<input type="hidden" name="{$field}" value="{$selectedNodeID}">
<div data-group-selector="{$identifier}"></div>
<script type="text/javascript">
jQuery(function() {
    $('[data-group-selector={$identifier}]').concreteTree({
            'treeID': {$treeID},
            'enableDragAndDrop': false,
            selectNodesByKey: [{$selectedNodeID}],
            'removeNodesByKey': [{$guestGroupNodeID}, {$registeredGroupNodeID}],
            onSelect : function(nodes) {
                if (nodes.length) {
                    $('input[name={$field}]').val(nodes[0]);
                } else {
                    $('input[name={$field}]').val('');
                }
            },
            chooseNodeInForm: 'single'
    });
});
</script>
EOL;

        return $html;
    }
}
