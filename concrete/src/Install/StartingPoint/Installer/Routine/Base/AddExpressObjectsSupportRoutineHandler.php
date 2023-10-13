<?php

namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Base;

use Concrete\Core\Tree\Node\NodeType;
use Concrete\Core\Tree\TreeType;
use Concrete\Core\Tree\Node\Type\ExpressEntryCategory;
use Concrete\Core\Tree\Type\ExpressEntryResults;
use Concrete\Block\ExpressForm\Controller as ExpressFormBlockController;
use Concrete\Core\User\Group\Group;

class AddExpressObjectsSupportRoutineHandler
{

    public function __invoke()
    {
        NodeType::add('express_entry_category');
        TreeType::add('express_entry_results');
        NodeType::add('express_entry_results');
        NodeType::add('express_entry_site_results');

        $tree = ExpressEntryResults::add();
        $node = $tree->getRootTreeNodeObject();

        // Add forms node beneath it.
        $forms = ExpressEntryCategory::add(ExpressFormBlockController::FORM_RESULTS_CATEGORY_NAME, $node);

        // Set the forms node to allow guests to post entries, since we're using it from the front-end.
        $forms->assignPermissions(
            Group::getByID(GUEST_GROUP_ID),
            ['add_express_entries']
        );

        // Set the root node to allow guests to view entries, so that blocks like express
        // entry list and express entry details work.
        $node->assignPermissions(
            Group::getByID(GUEST_GROUP_ID),
            ['view_express_entries']
        );
    }


}
