<?php

namespace Concrete\Core\Form\Service\Widget;

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Tree\Node\Type\Group as GroupTreeNode;
use Concrete\Core\Tree\Type\Group as GroupTree;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupList;
use Concrete\Core\Utility\Service\Identifier;

class GroupSelector
{
    protected $assetGroup;

    protected $request;

    protected $formHelper;

    protected $groupList;

    public function __construct(Form $formHelper, GroupList $groupList)
    {
        $this->assetGroup = ResponseAssetGroup::get();
        $this->request = Request::getInstance();
        $this->formHelper = $formHelper;
        $this->groupList = $groupList;
    }

    /**
     * @param string $field
     * @param \Concrete\Core\User\Group\Group|int|mixed $group
     * @param string|null $noneText
     */
    public function selectGroup($field, $group = null, $noneText = null)
    {
        if ($this->request->isMethod('POST') && $this->request->request->has($field)) {
            $group = $this->request->request->get($field);
        }
        if (is_numeric($group)) {
            $groupID = max((int) $group, 0);
        } elseif ($group instanceof Group) {
            $groupID = (int) ($group->getGroupID() ?? 0);
        } else {
            $groupID = 0;
        }
        $chooseText = (string) $noneText;
        if ($chooseText === '') {
            $chooseText = t('Choose a Group');
        }
        $identifier = app(Identifier::class)->getString(32);
        $htmlField = h($field);
        $htmlChooseText = h($chooseText);

        $selector = <<<EOT
<div data-concrete-group-input="{$identifier}">
    <concrete-group-input :group-id="{$groupID}" choose-text="{$htmlChooseText}" input-name="{$htmlField}"></concrete-group-input>
</div>
<script>
$(function() {
    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'div[data-concrete-group-input="{$identifier}"]',
            components: config.components,
        })
    })
});
</script>
EOT;

        print $selector;
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
                $selectedGroupID = (int) $this->request->request->get($field);
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

        return <<<EOL
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
    }
}
