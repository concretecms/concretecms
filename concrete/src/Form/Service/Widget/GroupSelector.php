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

    public function selectGroupWithTree($field, $group = null)
    {
        $this->assetGroup->requireAsset('core/groups');

        $identifier = new Identifier();
        $identifier = $identifier->getString(32);

        $selected = 0;

        if ($this->request->getRealMethod() == 'POST') {
            if ($this->request->request->has($field)) {
                $selected = intval($this->request->request->get($field));
            }
        } elseif ($group) {
            $selected = ($group instanceof Group) ? $group->getGroupID() : $group;
        }

        $tree = GroupTree::get();
        $guestGroupNode = GroupTreeNode::getTreeNodeByGroupID(GUEST_GROUP_ID);
        $registeredGroupNode = GroupTreeNode::getTreeNodeByGroupID(REGISTERED_GROUP_ID);

        $html = <<<EOL
        <input type="hidden" name="{$field}" value="{$selected}">
        <div data-group-selector="{$identifier}"></div>
        <script type="text/javascript">
        jQuery(function() {
            $('[data-group-selector={$identifier}]').concreteTree({
                    'treeID': '<?=$tree->getTreeID()?>',
                    'enableDragAndDrop': false,
                    selectNodesByKey: [{$selected}],
                    'removeNodesByKey': ['<?=$guestGroupNode->getTreeNodeID()?>','<?=$registeredGroupNode->getTreeNodeID()?>'],
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