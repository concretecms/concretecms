<?php

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\GroupFolder;
use Concrete\Core\Tree\Type\Group;

defined('C5_EXECUTE') or die("Access Denied.");

/** @var string $inputName */
/** @var int $rootTreeNodeID */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

$manager = Group::get();

if (!Node::getByID($rootTreeNodeID) instanceof GroupFolder) {
    $rootTreeNodeID = $manager->getRootTreeNodeID();
}

?>

<div class="controls">
    <div class="groups-tree" data-groups-tree="<?php echo $manager->getTreeID() ?>"></div>
    <?php echo $form->hidden($inputName) ?>

    <script type="text/javascript">
        $(function () {
            $('[data-groups-tree=<?php echo $manager->getTreeID()?>]').concreteTree({
                'treeID': '<?php echo $manager->getTreeID(); ?>',
                'chooseNodeInForm': 'single',
                'enableDragAndDrop': false,
                ajaxData: {
                    displayOnly: 'group_folder'
                },
                'selectNodesByKey': [<?php echo (int)$rootTreeNodeID?>],
                'onSelect': function (nodes) {
                    if (nodes.length) {
                        $('input[name=<?php echo h($inputName); ?>]').val(nodes[0]);
                    } else {
                        $('input[name=<?php echo h($inputName); ?>]').val('');
                    }
                }
            });
        });
    </script>
</div>