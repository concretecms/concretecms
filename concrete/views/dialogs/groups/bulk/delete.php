<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\Group\Group;
use Concrete\Core\View\View;

/** @var Group[] $groups */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
?>

<?php if (!is_array($groups) || count($groups) == 0) { ?>
    <div class="alert-message info">
        <?php echo t("No groups are eligible for this operation"); ?>
    </div>
<?php } else { ?>
    <p>
        <?php echo t('Are you sure you would like to delete the following groups?'); ?>
    </p>

    <form method="post" data-dialog-form="save-file-set" action="<?php echo $controller->action('submit'); ?>">
        <?php foreach ($groups as $group) { ?>
            <?php echo $form->hidden("item[]", $group->getGroupID()); ?>
        <?php } ?>

        <div class="ccm-ui">
            <?php /** @noinspection PhpUnhandledExceptionInspection */
            View::element('groups/confirm_list', ['groups' => $groups]); ?>
        </div>

        <div class="dialog-buttons">
            <button class="btn btn-secondary" data-dialog-action="cancel"><?php echo t('Cancel'); ?></button>
            <button type="button" data-dialog-action="submit" class="btn btn-primary ms-auto"><?php echo t('Delete'); ?></button>
        </div>
    </form>
<?php } ?>
