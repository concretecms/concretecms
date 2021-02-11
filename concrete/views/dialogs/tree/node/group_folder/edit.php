<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Controller\Dialog\Tree\Node\GroupFolder\Edit;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Validation\CSRF\Token;

/** @var Edit $controller */
/** @var Node $node */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);
?>

<div class="ccm-ui">
    <form method="post" data-dialog-form="edit-group-folder-node" class="form-horizontal"
          action="<?php echo $controller->action('update_group_folder_node') ?>">
        <?php echo $token->output('update_group_folder_node') ?>
        <?php echo $form->hidden("treeNodeID", $node->getTreeNodeID()); ?>

        <div class="form-group">
            <?php echo $form->label('treeNodeGroupFolderName', t('Name')) ?>
            <?php echo $form->text('treeNodeGroupFolderName', $node->getTreeNodeName()) ?>
        </div>

        <div class="dialog-buttons">
            <button class="btn btn-secondary" data-dialog-action="cancel">
                <?php echo t('Cancel') ?>
            </button>

            <button class="btn btn-primary float-right" data-dialog-action="submit" type="submit">
                <?php echo t('Update') ?>
            </button>
        </div>
    </form>

    <script type="text/javascript">
        $(function () {
            _.defer(function () {
                $('input[name=treeNodeGroupFolderName]').focus();
            });
            ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.updateTreeNode');
            ConcreteEvent.subscribe('AjaxFormSubmitSuccess.updateTreeNode', function (e, data) {
                if (data.form == 'edit-group-folder-node') {
                    ConcreteEvent.publish('ConcreteTreeUpdateTreeNode', {'node': data.response});
                }
            });
        });
    </script>
</div>

