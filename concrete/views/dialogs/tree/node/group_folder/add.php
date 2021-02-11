<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Controller\Dialog\Tree\Node\GroupFolder\Add;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Validation\CSRF\Token;

/** @var Add $controller */
/** @var Node $node */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);
?>

<div class="ccm-ui">
    <form method="post" data-dialog-form="add-group-folder-node" class="form-horizontal"
          action="<?php echo $controller->action('add_group_folder_node') ?>">
        <?php echo $token->output('add_group_folder_node') ?>
        <?php echo $form->hidden("treeNodeID", $node->getTreeNodeID()); ?>

        <div class="form-group">
            <?php echo $form->label('treeNodeGroupFolderName', t('Name')) ?>
            <?php echo $form->text('treeNodeGroupFolderName') ?>
        </div>

        <div class="dialog-buttons">
            <button class="btn btn-secondary" data-dialog-action="cancel">
                <?php echo t('Cancel') ?>
            </button>

            <button class="btn btn-primary float-right" data-dialog-action="submit" type="submit">
                <?php echo t('Add') ?>
            </button>
        </div>
    </form>

    <script type="text/javascript">
        $(function () {
            _.defer(function () {
                $('input[name=treeNodegroup_folderName]').focus();
            });
            ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.addTreeNode');
            ConcreteEvent.subscribe('AjaxFormSubmitSuccess.addTreeNode', function (e, data) {
                if (data.form == 'add-group-folder-node') {
                    ConcreteEvent.publish('ConcreteTreeAddTreeNode', {'node': data.response});
                }
            });
        });
    </script>
</div>